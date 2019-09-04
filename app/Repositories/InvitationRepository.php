<?php

namespace App\Repositories;

use App\Contracts\Repositories\InvitationRepositoryInterface;
use App\Invitation;
use App\Mail\InvitationMail;
use App\Team;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class InvitationRepository extends Repository implements InvitationRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Invitation::class;
    }

    /**
     * @param array    $invitationData
     * @param Team $team
     * @throws \Exception
     */
    public function sendInvitation(array $invitationData, Team $team): void
    {
        $user = User::where('email', $invitationData['email'])->first();
        $role = Role::where('name', $invitationData['role'])->first();

        if ($user && $team->users()->find($user->id)) {
            abort(403, 'This user is already a member of this team.');
        }

        if ($team->invitations()->where('email', $invitationData['email'])->first()) {
            abort(403, 'Invitation has been already sent to this email.');
        }

        $token = str_random(40);

        /**@var Invitation $invitation */
        $invitation = $team->invitations()->create([
            'uid'        => Uuid::uuid4(),
            'user_id'    => optional($user)->id,
            'role_id'    => $role->id,
            'email'      => $invitationData['email'],
            'token'      => hash('sha256', $token),
            'expires_at' => Carbon::now()->addWeek(),
        ]);

        Mail::to($invitationData['email'])->send(new InvitationMail($invitation, $team, $token));
    }

    /**
     * @param $inviteToken
     * @throws \Exception
     */
    public function acceptInvitation($inviteToken): void
    {
        $user = auth()->user();
        $invitation = Invitation::where('token', hash('sha256', $inviteToken))->first();

        if (!$invitation) {
            abort(403, 'Provided invitation token is wrong or has been already expired.');
        }

        if ($invitation->email != $user->email) {
            abort(403, 'Your email doesn\'t match invitation email. Please logout and try again.');
        }

        $user->attachRole($invitation->role, $invitation->team, ['online' => true]);

        $teams = $invitation->team->setChildConnection()
            ->teams()->whereIn('id', $invitation->team_ids)->get();

        foreach ($teams as $team) {
            $team->users()->attach($user);
        }

        $invitation->delete();
    }
}
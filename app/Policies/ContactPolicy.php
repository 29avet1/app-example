<?php

namespace App\Policies;

use App\Conversation;
use App\Contact;
use App\Team;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User    $user
     * @param Contact $contact
     * @param Team    $team
     * @return bool
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendMessage(User $user, Contact $contact, Team $team)
    {

        if ($user->hasPermission('manage_contacts', $team) && $contact->subscribed) {
            return true;
        }

        $this->deny('You can\'t send a message to this contact.');
    }
}

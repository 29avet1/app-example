<?php

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Team;
use Ramsey\Uuid\Uuid;

class TeamRepository extends Repository implements TeamRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Team::class;
    }

    /**
     * @param array $teamData
     * @param Team  $team
     * @throws \Exception
     */
    public function create(array $teamData, Team $team): void
    {
        $team = Team::create([
            'uid'     => Uuid::uuid4(),
            'team_id' => $team->id,
            'name'    => $teamData['name'],
        ]);

        if (count($teamData['user_ids'])) {
            $userIds = $team->users()
                ->whereIn('uid', $teamData['user_ids'])->pluck('id')->toArray();

            $team->users()->sync($userIds);
        }
    }

    /**
     * @param array $teamData
     * @param Team  $team
     */
    public function update(array $teamData, Team $team): void
    {
        $team->update([
            'name' => $teamData['name'],
        ]);

        $userIds = count($teamData['user_ids']) ? $team->users()
            ->whereIn('uid', $teamData['user_ids'])->pluck('id')->toArray() : [];

        $team->users()->sync($userIds);
    }

    /**
     * @param Team $team
     * @throws \Exception
     */
    public function delete(Team $team): void
    {
        if ($team->main) {
            abort(403, 'Main team can\'t be deleted.');
        }

        $userIds = $team->users()->pluck('id')->toArray();
        $mainTeam = Team::where('main', true)->first();
        $mainTeam->users()->sync($userIds, false);

        $team->delete();
    }
}
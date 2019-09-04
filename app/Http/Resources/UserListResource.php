<?php

namespace App\Http\Resources;

use App\Team;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserListResource extends ResourceCollection
{
    protected $team;

    /**
     * Create a new resource instance.
     *
     * @param  mixed        $resource
     * @param Team|null $team
     */
    public function __construct($resource, Team $team = null)
    {
        $this->team = $team;

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id'         => $user->uuid,
                'role'       => $user->roleInTeam($this->team, false)->name,
                'name'       => $user->name,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'avatar'     => $user->avatar,
                'online'     => $user->online,
                'available'  => $user->available,
            ];
        })->toArray();
    }
}

<?php namespace App\Http\Controllers\Api;


use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Team;
use App\User;
use Exception;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UsersController extends ApiController
{
    /**
     * Get all users of team
     * @param Team $team
     * @return UserListResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Team $team)
    {
        $this->authorize('member', $team);

        $users = $team->users()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->with(['roles' => function ($query) use ($team) {
                $query->wherePivot('team_id', $team->id);
            }])->get();

        return new UserListResource($users, $team);
    }

    /**
     * Get user info
     *
     * @throws Exception
     * @return UserResource
     *
     */
    public function show()
    {
        $user = auth()->user();

        return new UserResource($user);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles()
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json($user->roles);
    }
}
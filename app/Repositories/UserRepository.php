<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Services\Stripe\StripeService;
use App\Traits\FileUpload;
use App\User;
use Exception;
use Illuminate\Http\UploadedFile;

class UserRepository extends Repository implements UserRepositoryInterface
{
    use FileUpload;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * @param UploadedFile $file
     * @param User         $user
     * @return array
     */
    public function changeAvatar(UploadedFile $file, User $user): array
    {
        $filePath = "/user_{$user->uid}/avatars/";
        $fileData = $this->uploadImage($file, $filePath, 200);

        $user->avatar = $fileData['hash_name'];
        $user->save();

        return $fileData;
    }

    /**
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function getRoles(User $user): array
    {
        $teams = $user->teams()->select(['id', 'uid'])
            ->with(['roles' => function ($query) use ($user) {
                $query->where('role_user.user_id', $user->id);
            }])->get();

        $roles = [];
        foreach ($teams as $team) {
            $roles[] = [
                'team_id' => $team->uid,
                'role'        => $team->roles->first()->name,
            ];
        }

        return $roles;
    }
}
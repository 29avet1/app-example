<?php

namespace App\Http\Resources;

use App\TeamAddress;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        $accountOwner = $this->ownerToArray($this->owner);

        return [
            'id'                   => $this->uid,
            'user_role'            => $this->getUserRole()->name,
            'name'                 => $this->name,
            'email'                => $this->email,
            'website'              => $this->website,
            'logo'                 => $this->logo,
            'plan'                 => $this->plan,
            'slug'                 => $this->slug,
            'address'              => $this->address,
            'about'                => $this->about,
            'description'          => $this->description,
            'created_at'           => $this->created_at->toDateTimeString(),
            'updated_at'           => $this->updated_at->toDateTimeString(),
            'account_owner'        => $accountOwner,
        ];
    }

    private function ownerToArray(User $owner) {
        return [
            'id'         => $owner->uid,
            'name'       => $owner->name,
            'email'      => $owner->email,
            'avatar'     => $owner->avatar,
        ];
    }
}

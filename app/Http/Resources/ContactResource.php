<?php

namespace App\Http\Resources;

use App\ContactAddress;
use App\ContactData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @param bool                     $withMeta
     * @return array
     * @throws \Exception
     */
    public function toArray($request, $withMeta = true)
    {
        return [
            'id'         => $this->uid,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'name'       => $this->name,
            'avatar'     => $this->avatar,
            'subscribed' => $this->subscribed,
            'contacted'  => $this->contacted,
        ];
    }
}

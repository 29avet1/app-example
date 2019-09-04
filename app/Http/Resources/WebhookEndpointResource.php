<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEndpointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->uuid,
            'url'               => $this->url,
            'secret_key'        => $this->secret_key,
            'active'            => $this->active,
            'created_at'        => $this->created_at->toDateTimeString(),
            'selected_webhooks' => $this->selected_webhooks->pluck('type')->toArray(),
        ];
    }
}

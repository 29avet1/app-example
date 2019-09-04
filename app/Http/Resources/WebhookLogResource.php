<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhookLogResource extends JsonResource
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
            'id'                   => $this->id,
            'endpoint_url'         => $this->endpoint->url,
            'response_status_code' => $this->response_status_code,
            'type'                 => $this->type,
            'request_body'         => $this->request_body,
            'response_body'        => $this->response_body,
            'created_at'           => $this->created_at->toDateTimeString(),
        ];
    }
}

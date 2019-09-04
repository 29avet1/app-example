<?php

namespace App\Listeners\Webhooks;

use App\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleContactCreateWebhook extends WebhookListener implements ShouldQueue
{
    protected $webhookType = 'contact.created';

    /**
     * @param $event
     * @return array
     */
    protected function configureWebhookData($event): array
    {
        /** @var Contact $contact */
        $contact = $event->contact;

        return [
            'contact' => [
                'id'              => $contact->uuid,
                'phone'           => $contact->phone,
                'name'            => $contact->name,
                'created_at'      => $contact->created_at->toDateTimeString(),
            ],
        ];
    }
}

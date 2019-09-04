<?php

namespace App\Listeners\Webhooks;

use App\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleUnsubscribeWebhook extends WebhookListener implements ShouldQueue
{
    protected $webhookType = 'contact.unsubscribed';

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
                'subscribed_via'  => $contact->subscribed_via,
                'contacted'       => $contact->contacted,
                'created_at'      => $contact->created_at->toDateTimeString(),
                'unsubscribed_at' => $contact->unsubscribed_at->toDateTimeString(),
            ],
        ];
    }
}

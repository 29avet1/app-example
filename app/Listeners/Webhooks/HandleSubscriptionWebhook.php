<?php

namespace App\Listeners\Webhooks;

use App\Contact;
use App\ContactList;
use App\ContactListPivot;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleSubscriptionWebhook extends WebhookListener implements ShouldQueue
{
    protected $webhookType = 'contact.subscription_created';

    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->subscriptionUpdated) {
            $this->webhookType = 'contact.subscription_updated';
        }

        parent::handle($event);
    }

    /**
     * @param $event
     * @return array
     */
    protected function configureWebhookData($event): array
    {
        /** @var Contact $contact */
        $contact = $event->contact;
        /** @var ContactList $list */
        $list = $event->list;
        /** @var ContactListPivot $listPivot */
        $listPivot = $contact->list_pivot()->where('list_id', $list->id)->first();

        return [
            'contact' => [
                'id'                      => $contact->uuid,
                'phone'                   => $contact->phone,
                'name'                    => $contact->name,
                'created_at'              => $contact->created_at->toDateTimeString(),
                'subscribed_at'           => $listPivot->subscribed_at->toDateTimeString(),
            ],
        ];
    }
}

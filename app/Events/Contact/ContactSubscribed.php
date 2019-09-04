<?php

namespace App\Events\Contact;

use App\Contact;
use App\ContactList;
use App\Team;
use Illuminate\Foundation\Events\Dispatchable;

class ContactSubscribed
{
    use Dispatchable;
    /**
     * @var Team
     */
    public $team;
    /**
     * @var ContactList
     */
    public $list;
    /**
     * @var Contact
     */
    public $contact;
    /**
     * @var bool
     */
    public $subscriptionUpdated;
    /**
     * @var bool
     */
    public $subscribedByChat;

    /**
     * Create a new event instance.
     *
     * @param Team    $team
     * @param ContactList $list
     * @param Contact     $contact
     * @param bool        $subscriptionUpdated
     * @param bool        $subscribedByChat
     */
    public function __construct(Team $team, ContactList $list, Contact $contact, bool $subscriptionUpdated = false, bool $subscribedByChat = false)
    {
        $this->team = $team;
        $this->list = $list;
        $this->contact = $contact;
        $this->subscriptionUpdated = $subscriptionUpdated;
        $this->subscribedByChat = $subscribedByChat;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['team:' . $this->team->slug];
    }
}

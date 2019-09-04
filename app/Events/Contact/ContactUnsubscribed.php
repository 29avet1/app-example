<?php

namespace App\Events\Contact;

use App\Contact;
use App\Team;
use Illuminate\Foundation\Events\Dispatchable;

class ContactUnsubscribed
{
    use Dispatchable;
    /**
     * @var Team
     */
    public $team;
    /**
     * @var Contact
     */
    public $contact;

    /**
     * Create a new event instance.
     *
     * @param Team $team
     * @param Contact  $contact
     */
    public function __construct(Team $team, Contact $contact)
    {
        $this->team = $team;
        $this->contact = $contact;
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

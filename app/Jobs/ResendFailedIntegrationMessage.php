<?php

namespace App\Jobs;

use App\Contact;
use App\Integration;
use App\Team;
use App\Traits\Jobs\IntegrationRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ResendFailedIntegrationMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, IntegrationRequests;
    /**
     * @var Team
     */
    private $team;
    /**
     * @var Integration
     */
    private $integration;
    /**
     * @var Contact
     */
    private $contact;
    /**
     * @var array
     */
    private $message;
    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 30;

    /**
     * Create a new job instance.
     *
     * @param Team    $team
     * @param Integration $integration
     * @param Contact     $contact
     * @param array       $message
     */
    public function __construct(Team $team, Integration $integration, Contact $contact, array $message)
    {
        $this->queue = 'integration';
        $this->delay = 30;
        $this->team = $team;
        $this->integration = $integration;
        $this->contact = $contact;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->integration->app) {
            case 'front':
                $this->sendMessageToFront($this->team, $this->integration, $this->contact, $this->message);
                break;
            default:
                break;
        }
    }
}

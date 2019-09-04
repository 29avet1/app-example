<?php

namespace App\Jobs;

use App\Team;
use App\SelectedWebhook;
use App\Traits\Jobs\WebhookRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ResendFailedWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, WebhookRequest;

    /**
     * @var string
     */
    protected $webhookType;
    /**
     * @var Team
     */
    protected $team;
    /**
     * @var SelectedWebhook
     */
    protected $webhook;
    /**
     * @var array
     */
    private $webhookData;
    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 30;

    /**
     * Create a new job instance.
     *
     * @param Team        $team
     * @param SelectedWebhook $webhook
     * @param array           $webhookData
     */
    public function __construct(Team $team, SelectedWebhook $webhook, array $webhookData)
    {
        $this->queue = 'webhook';
        $this->delay = 30;
        $this->team = $team;
        $this->webhook = $webhook;
        $this->webhookData = $webhookData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $endpoint = $this->webhook->endpoint;
        $response = $this->request($this->webhookData, $endpoint);

        $endpoint->webhook_logs()->create([
            'team_id'          => $this->team->id,
            'response_status_code' => $response['status'],
            'type'                 => $this->webhook->type,
            'request_body'         => json_encode($this->webhookData, JSON_PRETTY_PRINT),
            'response_body'        => $response['body'],
        ]);

        if (strpos($response['status'], '2') !== 0) {
            abort(400, 'Request to webhook endpoint failed.');
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return [
            'team:' . $this->team->slug,
            'webhook:' . $this->webhook->type,
        ];
    }
}

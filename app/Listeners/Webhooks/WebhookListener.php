<?php

namespace App\Listeners\Webhooks;

use App\Jobs\ResendFailedWebhook;
use App\Team;
use App\SelectedWebhook;
use App\Traits\Jobs\WebhookRequest;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;

abstract class WebhookListener
{
    use WebhookRequest;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'webhook';
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var string
     */
    protected $webhookType;
    /**
     * @var Team
     */
    protected $team;

    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        $this->team = $event->team->setChildConnection();

        if (!$this->team->hasWebhooksEnabled()) {
            return;
        }

        $webhooks = $this->getSelectedWebhooks($event->team);
        if (!$webhooks->isEmpty()) {
            $webhookData = array_merge($this->webhookHeadData(), $this->configureWebhookData($event));

            foreach ($webhooks as $webhook) {
                $this->handleWebhook($webhookData, $webhook);
            }
        }
    }

    /**
     * @param array           $webhookData
     * @param SelectedWebhook $webhook
     */
    protected function handleWebhook(array $webhookData, SelectedWebhook $webhook)
    {
        $endpoint = $webhook->endpoint;
        $response = $this->request($webhookData, $endpoint);

        $endpoint->webhook_logs()->create([
            'team_id'          => $this->team->id,
            'response_status_code' => $response['status'],
            'type'                 => $webhook->type,
            'request_body'         => json_encode($webhookData, JSON_PRETTY_PRINT),
            'response_body'        => $response['body'],
        ]);

        if (strpos($response['status'], '2') !== 0) {
            ResendFailedWebhook::dispatch($this->team, $webhook, $webhookData);
        }
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param \App\Events\OrderPlaced $event
     * @return bool
     */
    public function shouldQueue($event)
    {
        /** @var Team $team */
        $team = $event->team->setChildConnection();

        return $team->hasWebhooksEnabled();
    }

    /**
     * @param Team $team
     * @return Collection
     */
    protected function getSelectedWebhooks(Team $team): Collection
    {
        $team->setChildConnection();

        return $team->selected_webhooks()
            ->where('type', $this->webhookType)
            ->whereHas('endpoint', function ($query) {
                $query->where('active', true);
            })
            ->with('endpoint')->get();
    }

    /**
     * @return array
     */
    private function webhookHeadData(): array
    {
        return [
            'trigger'  => $this->webhookType,
            'team' => [
                'id'   => $this->team->uid,
                'name' => $this->team->name,
            ],
        ];
    }

    protected abstract function configureWebhookData($event): array;
}
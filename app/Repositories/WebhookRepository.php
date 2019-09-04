<?php

namespace App\Repositories;

use App\Contracts\Repositories\WebhookRepositoryInterface;
use App\Team;
use App\WebhookEndpoint;
use App\WebhookLog;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ramsey\Uuid\Uuid;

class WebhookRepository extends Repository implements WebhookRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return WebhookEndpoint::class;
    }

    /**
     * @param array    $webhookData
     * @param Team $team
     * @return WebhookEndpoint
     * @throws \Exception
     */
    public function create(array $webhookData, Team $team): WebhookEndpoint
    {
        $webhook = WebhookEndpoint::create([
            'uid'         => Uuid::uuid4(),
            'team_id' => $team->id,
            'url'         => $webhookData['url'],
            'active'      => $webhookData['active'],
            'secret_key'    => encrypt(str_random(64)),
        ]);

        foreach ($webhookData['types'] as $webhookType) {
            $webhook->selected_webhooks()->create([
                'team_id' => $team->id,
                'type'        => $webhookType,
            ]);
        }

        return $webhook;
    }

    /**
     * @param array           $webhookData
     * @param Team        $team
     * @param WebhookEndpoint $webhook
     * @return WebhookEndpoint
     */
    public function update(array $webhookData, Team $team, WebhookEndpoint $webhook): WebhookEndpoint
    {
        $webhook->update(['active' => $webhookData['active']]);

        foreach ($webhookData['types'] as $webhookType) {
            $webhook->selected_webhooks()->firstOrCreate([
                'type' => $webhookType,
            ], [
                'team_id' => $team->id,
            ]);
        }

        $webhook->selected_webhooks()->whereNotIn('type', $webhookData['types'])->delete();

        return $webhook;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getLogList(array $filters): LengthAwarePaginator
    {
        $webhookLogs = WebhookLog::with(['endpoint']);

        if (@$filters['type']) {
            $webhookLogs->where('type', $filters['type']);
        }
        if (@$filters['start_date']) {
            $startDate = Carbon::createFromTimestamp($filters['start_date']);
            $webhookLogs->where('created_at', '>=', $startDate);
        }
        if (@$filters['end_date']) {
            $endDate = Carbon::createFromTimestamp($filters['end_date']);
            $webhookLogs->where('created_at', '<=', $endDate);
        }

        return $webhookLogs
            ->orderByDesc('created_at')
            ->paginate($filters['limit'] ?? 25);
    }
}
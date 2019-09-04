<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\WebhookRepositoryInterface;
use App\Http\Requests\WebhookCreateRequest;
use App\Http\Requests\WebhookLogRequest;
use App\Http\Requests\WebhookUpdateRequest;
use App\Team;
use App\Repositories\WebhookRepository;
use App\WebhookEndpoint;

class WebhooksController extends Controller
{
    /**
     * @var WebhookRepository
     */
    private $webhookRepository;

    /**
     * ContactsController constructor.
     * @param WebhookRepositoryInterface $webhookRepository
     */
    public function __construct(WebhookRepositoryInterface $webhookRepository)
    {
        $this->webhookRepository = $webhookRepository;
    }

    /**
     * @param Team $team
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Team $team)
    {
        $this->authorize('manageSettings', $team);

        $webhooks = WebhookEndpoint::with('selected_webhooks')
            ->orderBy('created_at')->get();

        return view('webhooks.index', compact('webhooks'));
    }

    /**
     * @param Team $team
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Team $team)
    {
        $this->authorize('manageSettings', $team);

        $webhookTypes = webhook_types(true);

        return view('webhooks.create', compact('webhookTypes'));
    }


    /**
     * @param WebhookCreateRequest $request
     * @param Team             $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(WebhookCreateRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $webhookData = $request->only([
            'url',
            'active',
            'types',
        ]);

        $this->webhookRepository->create($webhookData, $team);

        flash(['message' => 'Webhook has been successfully created.']);

        return redirect()->route('webhooks.index');
    }

    /**
     * @param WebhookLogRequest $request
     * @param Team              $team
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function logs(WebhookLogRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $filters = $request->only([
            'page',
            'limit',
            'type',
            'start_date',
            'end_date'
        ]);
        $webhookLogs = $this->webhookRepository->getLogList($filters);

        return view('webhooks.logs', compact('webhookLogs'));
    }

    /**
     * @param WebhookUpdateRequest $request
     * @param Team                 $team
     * @param WebhookEndpoint      $webhook
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(WebhookUpdateRequest $request, Team $team, WebhookEndpoint $webhook)
    {
        $this->authorize('manageSettings', $team);

        $webhookData = $request->only([
            'active',
            'types',
        ]);

        $this->webhookRepository->update($webhookData, $team, $webhook);

        flash(['message' => 'Webhook has been successfully updated.']);

        return redirect()->route('webhooks.index');
    }

    /**
     * @param Team        $team
     * @param WebhookEndpoint $webhook
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(Team $team, WebhookEndpoint $webhook)
    {
        $this->authorize('manageSettings', $team);

        $webhook->delete();

        flash(['message' => 'Webhook has been successfully deleted.']);

        return redirect()->route('webhooks.index');
    }
}

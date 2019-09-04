<?php namespace App\Http\Controllers;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Http\Resources\TeamListResource;
use App\Http\Resources\TeamResource;
use App\Team;
use App\Repositories\TeamRepository;
use App\Traits\Controllers\HasAnalytics;
use Exception;

/**
 * Class TeamController
 * @package App\Http\Controllers\Api
 */
class TeamsController extends Controller
{
    use HasAnalytics;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * TeamsController constructor.
     * @param TeamRepositoryInterface $teamRepository
     */
    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * Get all teams of the user
     *
     * @throws Exception
     */
    public function index()
    {
        $teams = auth()->user()->teams()->orderByDesc('created_at')->get();
        $teams->load([
            'subscription',
            'users' => function ($query) {
                $query->select(['id', 'uid']);
            }
        ]);

        return TeamListResource::collection($teams);
    }

    /**
     * Create new team action
     *
     */
    public function store(TeamCreateRequest $request)
    {
        $team = $this->teamRepository->create($request->all());

        return response()->json([
            'message'     => 'Team has been successfully created',
            'team_id' => $team->uid,
        ]);
    }

    /**
     * Get team by id and user
     */
    public function show(Team $team)
    {
        $this->authorize('member', $team);

        $team->load([
            'owner',
            'whatsapp_account',
            'business_address',
        ]);

        $team->unread_conversations_count = $this->teamRepository->getUnreadConversationsCount($team);

        return new TeamResource($team);
    }

    /**
     * Update team info
     *
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $teamData = $request->only([
            'name',
            'website',
            'email',
            'address',
            'about',
            'description',
        ]);

        $this->teamRepository->update($teamData, $team);

        return response()->json(['message' => 'Team data has been successfully updated.']);
    }

    /**
     * Delete team
     *
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     */
    public function delete(Team $team)
    {
        $this->authorize('owns', $team);

        $this->teamRepository->deleteFiles($team);

        return response()->json(['message' => 'Team has been successfully deleted.']);
    }
}
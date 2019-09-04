<?php namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Http\Resources\TeamListResource;
use App\Http\Resources\TeamResource;
use App\Team;
use App\Repositories\TeamRepository;
use App\Traits\Controllers\HasAnalytics;
use Exception;
use Illuminate\Support\Facades\Artisan;

/**
 * Class TeamController
 * @package App\Http\Controllers\Api
 */
class TeamsController extends ApiController
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
     * Get all teams by user
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws Exception
     *
     * @SWG\Get(
     *      path="/teams",
     *      tags={"Teams"},
     *      summary="Get all teams by user",
     *      operationId="api.teams.all",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="array",
     *            @SWG\Items(
     *                @SWG\Property(property="id", type="integer"),
     *                @SWG\Property(property="name", type="string"),
     *                @SWG\Property(property="legal_team_name", type="string"),
     *                @SWG\Property(property="legal_entity_type", type="string"),
     *                @SWG\Property(property="website", type="string"),
     *                @SWG\Property(property="email", type="string"),
     *                @SWG\Property(property="logo", type="string"),
     *                @SWG\Property(property="plan", type="string"),
     *                @SWG\Property(property="slug", type="string"),
     *                @SWG\Property(property="created_at", type="string"),
     *                @SWG\Property(property="updated_at", type="string"),
     *            )
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function index()
    {
        $teams = auth()->user()->teams()->orderByDesc('created_at')->get();
        $teams->setChildConnection()->load([
            'subscription',
            'users' => function ($query) {
                $query->select(['id', 'uid']);
            }
        ]);

        foreach ($teams as $team) {
            // need to load inside foreach, cause conversations are loading from different schemas
            $team->unread_conversations_count = $this->teamRepository
                ->getUnreadConversationsCount($team);
        }

        return TeamListResource::collection($teams);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSubscriptions()
    {
        $teams = auth()->user()->teams()
            ->select(['id', 'uid'])
            ->with(['subscription', 'subscription.payment_plan'])->get();

        return TeamSubscriptionsResource::collection($teams);
    }

    /**
     * Create new team action
     *
     * @param TeamCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     * @SWG\Post(
     *      path="/teams",
     *      tags={"Teams"},
     *      summary="Create new team action",
     *      operationId="api.teams.create",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="name", type="string"),
     *            @SWG\Property(property="legal_team_name", type="string"),
     *            @SWG\Property(property="legal_entity_type", type="string"),
     *            @SWG\Property(property="website", type="string"),
     *            @SWG\Property(property="logo", type="string"),
     *            @SWG\Property(property="slug", type="string"),
     *            @SWG\Property(
     *              property="business_address",
     *              type="object",
     *              @SWG\Property(property="city", type="string"),
     *              @SWG\Property(property="line", type="string"),
     *              @SWG\Property(property="postal_code", type="string"),
     *              @SWG\Property(property="state", type="string"),
     *              @SWG\Property(property="country_code", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="messaging_platforms",
     *              type="object",
     *              @SWG\Property(
     *                  property="whatsapp",
     *                  type="object",
     *                  @SWG\Property(property="phone", type="string")
     *              )
     *            ),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string"),
     *            @SWG\Property(property="team_id", type="string"),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
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
     *
     * @param Team $team
     * @return TeamResource
     * @throws Exception
     *
     * @SWG\Get(
     *      path="/teams/{team_id}",
     *      tags={"Teams"},
     *      summary="Get team by id and user",
     *      operationId="api.teams.get",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="id", type="string"),
     *            @SWG\Property(property="user_role", type="string"),
     *            @SWG\Property(property="name", type="string"),
     *            @SWG\Property(property="legal_team_name", type="string"),
     *            @SWG\Property(property="legal_entity_type", type="string"),
     *            @SWG\Property(property="email", type="string"),
     *            @SWG\Property(property="website", type="string"),
     *            @SWG\Property(property="logo", type="string"),
     *            @SWG\Property(property="plan", type="string"),
     *            @SWG\Property(property="slug", type="string"),
     *            @SWG\Property(property="address", type="string"),
     *            @SWG\Property(property="about", type="string"),
     *            @SWG\Property(property="description", type="string"),
     *            @SWG\Property(property="created_at", type="string"),
     *            @SWG\Property(property="updated_at", type="string"),
     *            @SWG\Property(
     *              property="account_owner",
     *              type="object",
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="created_at", type="string"),
     *              @SWG\Property(property="updated_at", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="business_address",
     *              type="object",
     *              @SWG\Property(
     *                  property="country",
     *                  type="object",
     *                  @SWG\Property(property="name", type="string"),
     *                  @SWG\Property(property="code", type="string"),
     *              ),
     *              @SWG\Property(property="city", type="string"),
     *              @SWG\Property(property="line", type="string"),
     *              @SWG\Property(property="postal_code", type="string"),
     *              @SWG\Property(property="state", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="payment_provider",
     *              type="object",
     *                 @SWG\Property(property="provider", type="string"),
     *                 @SWG\Property(property="public_key", type="string"),
     *                 @SWG\Property(property="currency", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="messaging_platforms",
     *              type="object",
     *              @SWG\Property(
     *                  property="whatsapp",
     *                  type="object",
     *                  @SWG\Property(property="phone", type="string")
     *              )
     *            )
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function show(Team $team)
    {
        $this->authorize('member', $team);

        $team->setChildConnection()->load([
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
     * @param TeamUpdateRequest $request
     * @param Team              $team
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     * @SWG\Put(
     *      path="/teams/{team_id}",
     *      tags={"Teams"},
     *      summary="Update team info",
     *      operationId="api.teams.update",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="name", type="string"),
     *            @SWG\Property(property="email", type="string"),
     *            @SWG\Property(property="website", type="string"),
     *            @SWG\Property(property="slug", type="string"),
     *            @SWG\Property(property="address", type="string"),
     *            @SWG\Property(property="about", type="string"),
     *            @SWG\Property(property="description", type="string"),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string"),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
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
     * Update team business info
     *
     * @param TeamUpdateBusinessDataRequest $request
     * @param Team                          $team
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/business",
     *      tags={"Teams"},
     *      summary="Update team business info",
     *      operationId="api.teams.update_business",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="legal_team_name", type="string"),
     *            @SWG\Property(property="legal_entity_type", type="string"),
     *            @SWG\Property(
     *              property="business_address",
     *              type="object",
     *              @SWG\Property(property="city", type="string"),
     *              @SWG\Property(property="line", type="string"),
     *              @SWG\Property(property="postal_code", type="string"),
     *              @SWG\Property(property="state", type="string"),
     *              @SWG\Property(property="country", type="string"),
     *            ),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string"),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function updateBusiness(TeamUpdateBusinessDataRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $teamData = $request->only([
            'legal_team_name',
            'legal_entity_type',
            'business_address',
        ]);
        $this->teamRepository->update($teamData, $team);

        return response()->json(['message' => 'Team business info has been successfully updated.']);
    }

    /**
     * Update team logo
     *
     * @param TeamUpdateLogoRequest $request
     * @param Team                  $team
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     * @SWG\Post(
     *      path="/teams/{team_id}/logo",
     *      tags={"Teams"},
     *      summary="Update team logo",
     *      operationId="api.teams.update.logo",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="logo", type="string"),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string"),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string"),
     *          )
     *      )
     * )
     */
    public function updateLogo(TeamUpdateLogoRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $file = $this->teamRepository->changeLogo($request->logo, $team);

        return response()->json([
            'logo' => $file['url'],
        ]);
    }

    /**
     * @param Team $team
     * @return TeamSettingsResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/settings",
     *      tags={"Team"},
     *      summary="get team settings.",
     *      operationId="api.team.settings.get",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *           name="Authorization",
     *           in="header",
     *           required=true,
     *           type="string",
     *           default="Bearer <token>",
     *           description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *           name="team_id",
     *           in="path",
     *           required=true,
     *           type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="available", type="boolean"),
     *            @SWG\Property(property="available_till", type="string"),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function getSettings(Team $team)
    {
        $this->authorize('member', $team);

        return new TeamSettingsResource($team->setting);
    }

    /**
     * @param Team $team
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/availabilities",
     *      tags={"Team"},
     *      summary="get team availabilities.",
     *      operationId="api.team.availabilities.get",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *           name="Authorization",
     *           in="header",
     *           required=true,
     *           type="string",
     *           default="Bearer <token>",
     *           description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *           name="team_id",
     *           in="path",
     *           required=true,
     *           type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="day", type="string"),
     *                @SWG\Property(property="from", type="string"),
     *                @SWG\Property(property="to", type="string"),
     *                @SWG\Property(property="available", type="boolean"),
     *              ),
     *            )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function getAvailabilities(Team $team)
    {
        $this->authorize('manageSettings', $team);

        $availabilities = Availability::all();

        return TeamAvailabilityResource::collection($availabilities);
    }

    /**
     * @param TeamAvailabilitiesUpdateRequest $request
     * @param Team                            $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *    path="/teams/{team_id}/availability-times",
     *    tags={"Teams"},
     *    summary="Update team availability time",
     *    operationId="api.teams.availability_times.update",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *        name="Authorization",
     *        in="header",
     *        required=true,
     *        type="string",
     *        default="Bearer <token>",
     *        description="Authorization"
     *    ),
     *    @SWG\Parameter(
     *        name="team_id",
     *        in="path",
     *        required=true,
     *        type="string"
     *    ),
     *    @SWG\Parameter(
     *        name="body",
     *        in="body",
     *        required=true,
     *        @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *            property="permissions",
     *            type="object",
     *            @SWG\Property(property="timezone", type="string"),
     *            @SWG\Property(
     *                property="availabilities",
     *                type="object",
     *                @SWG\Property(
     *                    property="monday",
     *                    type="object",
     *                    @SWG\Property(property="from", type="string"),
     *                    @SWG\Property(property="to", type="string"),
     *                    @SWG\Property(property="available", type="string"),
     *                )
     *            ),
     *          ),
     *        ),
     *    ),
     *    @SWG\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @SWG\Response(
     *         response=403,
     *         description="Permission Denied"
     *     ),
     *    @SWG\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *    @SWG\Response(
     *        response=500,
     *        description="Internal Server Error",
     *        @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="message", type="string")
     *        )
     *    )
     * )
     */
    public function updateAvailabilities(TeamAvailabilitiesUpdateRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $team->setting->update(['timezone' => $request->timezone]);
        $this->teamRepository->updateAvailabilityTimes($request->availabilities);

        // refresh current availability time
        $available = $team->setting->available;
        $team->setting()->update([
            'available_till' => null,
            'away_till'      => null,
        ]);
        $this->teamRepository->updateAvailability($available, $team);

        return response()->json(['message' => 'Team availabilities have been successfully updated.']);
    }

    /**
     * Get team analytics
     *
     * @param AnalyticsRequest                   $request
     * @param Team                           $team
     * @param ContactRepositoryInterface         $contactRepository
     * @param ConversationRepositoryInterface    $conversationRepository
     * @param ContactListRepositoryInterface     $contactListRepository
     * @param TemplateMessageRepositoryInterface $templateMessageRepository
     * @param AutomationRepositoryInterface      $automationRepository
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @SWG\Get(
     *      path="/teams/{team_id}/analytics",
     *      tags={"Teams"},
     *      operationId="api.teams.analytics",
     *      summary="Get team analytics",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="date_point",
     *          in="query",
     *          required=true,
     *          type="string",
     *          description="'day','week','month'",
     *      ),
     *      @SWG\Parameter(
     *          name="time_zone",
     *          in="query",
     *          required=false,
     *          type="string",
     *          description="https://www.php.net/manual/en/timezones.php",
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="start_date",
     *          in="query",
     *          required=true,
     *          description="Timestamp",
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="end_date",
     *          in="query",
     *          required=true,
     *          description="Timestamp",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(
     *              property="date_points",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="timestamp", type="integer"),
     *              )
     *            ),
     *            @SWG\Property(
     *              property="contacts",
     *              type="object",
     *              @SWG\Property(property="total", type="integer"),
     *              @SWG\Property(
     *                property="new",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="all",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="contact_activities",
     *              type="object",
     *              @SWG\Property(
     *                property="daily_contacted",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="subscribers",
     *              type="object",
     *              @SWG\Property(property="total", type="integer"),
     *              @SWG\Property(
     *                property="all",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="subscribed",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="unsubscribed",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="messages",
     *              type="object",
     *              @SWG\Property(property="total_inbound", type="integer"),
     *              @SWG\Property(property="total_outbound", type="integer"),
     *              @SWG\Property(property="total_conversations", type="integer"),
     *              @SWG\Property(
     *                property="inbound",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="outbound",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="conversations",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="message_responses",
     *              type="object",
     *              @SWG\Property(property="average_resolution_time", type="integer"),
     *              @SWG\Property(
     *                property="total_first_replies",
     *                type="object",
     *                  @SWG\Property(
     *                    property="0-1",
     *                    type="array",
     *                    @SWG\Items(
     *                      @SWG\Property(property="percent", type="double"),
     *                    )
     *                  ),
     *                  @SWG\Property(
     *                    property="1-8",
     *                    type="array",
     *                    @SWG\Items(
     *                      @SWG\Property(property="percent", type="double"),
     *                    )
     *                  ),
     *                  @SWG\Property(
     *                    property="8-24",
     *                    type="array",
     *                    @SWG\Items(
     *                      @SWG\Property(property="percent", type="double"),
     *                    )
     *                  ),
     *                  @SWG\Property(
     *                    property=">24",
     *                    type="array",
     *                    @SWG\Items(
     *                      @SWG\Property(property="percent", type="double"),
     *                    )
     *                  ),
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="sent_template_messages",
     *              type="object",
     *              @SWG\Property(property="total", type="integer"),
     *              @SWG\Property(
     *                property="sent",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="failed",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="automation",
     *              type="object",
     *              @SWG\Property(
     *                property="unique_interactions",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *              @SWG\Property(
     *                property="all_interactions",
     *                type="array",
     *                @SWG\Items(
     *                  @SWG\Property(property="count", type="integer"),
     *                )
     *              ),
     *            ),
     *            @SWG\Property(
     *              property="contact_lists",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="id", type="string"),
     *                @SWG\Property(property="name", type="string"),
     *                @SWG\Property(property="new_subscribed", type="integer"),
     *                @SWG\Property(property="all_subscribed", type="integer"),
     *              )
     *            ),
     *            @SWG\Property(
     *              property="template_messages",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="keyword", type="string"),
     *                @SWG\Property(property="sent", type="integer"),
     *                @SWG\Property(property="failed", type="integer"),
     *              )
     *            ),
     *            @SWG\Property(
     *              property="keywords",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="id", type="string"),
     *                @SWG\Property(property="keyword", type="string"),
     *                @SWG\Property(property="all_interactions", type="integer"),
     *                @SWG\Property(property="unique_interactions", type="integer"),
     *              )
     *            ),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function analytics(
        AnalyticsRequest $request,
        Team $team,
        ContactRepositoryInterface $contactRepository,
        ConversationRepositoryInterface $conversationRepository,
        ContactListRepositoryInterface $contactListRepository,
        TemplateMessageRepositoryInterface $templateMessageRepository,
        AutomationRepositoryInterface $automationRepository
    )
    {
        $this->authorize('viewAnalytics', $team);

        $intervals = $this->getDateIntervals($request->start_date, $request->end_date, $request->time_zone, $request->date_point);
        $datePeriod = $intervals['period'];
        $dateIntervals = $intervals['intervals'];
        $startDay = $intervals['start_day'];
        $endDay = $intervals['end_day'];

        $analytics = [
            'date_points'            => to_timestamp($datePeriod),
            'contacts'               => $contactRepository->getAnalytics($endDay, $dateIntervals),
            'contact_activities'     => $contactRepository->getActivityAnalytics($dateIntervals),
            'subscribers'            => $contactRepository->getSubscriberAnalytics($endDay, $dateIntervals),
            'messages'               => $conversationRepository->getMessageAnalytics($startDay, $endDay, $dateIntervals),
            'message_responses'      => $conversationRepository->getMessageResponseAnalytics($startDay, $endDay),
            'contact_lists'          => $contactListRepository->getAnalytics($startDay, $endDay),
            'sent_template_messages' => $templateMessageRepository->getSentTemplatesAnalytics($startDay, $endDay, $dateIntervals),
            'template_messages'      => $templateMessageRepository->getTemplateMessagesAnalytics($startDay, $endDay),
            'automation'             => $automationRepository->getAnalytics($dateIntervals),
            'keywords'               => $automationRepository->getKeywordAnalytics($startDay, $endDay),
        ];

        return response()->json($analytics);
    }

    /**
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/permissions",
     *      tags={"Teams"},
     *      summary="Get team role - permission relations",
     *      operationId="api.teams.permissions.get",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(
     *                property="manage_settings",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_automation",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_contacts",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_contacts",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_invoices",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_invoices",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_analytics",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *          ),
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function getPermissions(Team $team)
    {
        $this->authorize('member', $team);

        $permissionRoles = $this->teamRepository->getPermissionRoleIds($team);

        return response()->json($permissionRoles);
    }

    /**
     * @param TeamUpdatePermissionsRequest $request
     * @param Team                         $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *    path="/teams/{team_id}/permissions",
     *    tags={"Teams"},
     *    summary="Update team role - permission relations",
     *    operationId="api.teams.permissions.update",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *        name="Authorization",
     *        in="header",
     *        required=true,
     *        type="string",
     *        default="Bearer <token>",
     *        description="Authorization"
     *    ),
     *    @SWG\Parameter(
     *        name="team_id",
     *        in="path",
     *        required=true,
     *        type="string"
     *    ),
     *    @SWG\Parameter(
     *        name="body",
     *        in="body",
     *        required=true,
     *        @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *            property="permissions",
     *            type="object",
     *            @SWG\Property(
     *                property="manage_settings",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_automation",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_contacts",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_contacts",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="manage_invoices",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_invoices",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *            @SWG\Property(
     *                property="view_analytics",
     *                type="array",
     *                @SWG\Items(
     *                    @SWG\Property(property="role", type="string"),
     *                ),
     *            ),
     *          ),
     *        ),
     *    ),
     *    @SWG\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @SWG\Response(
     *         response=403,
     *         description="Permission Denied"
     *     ),
     *    @SWG\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *    @SWG\Response(
     *        response=500,
     *        description="Internal Server Error",
     *        @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="message", type="string")
     *        )
     *    )
     * )
     */
    public function updatePermissions(TeamUpdatePermissionsRequest $request, Team $team)
    {
        $this->authorize('manageSettings', $team);

        $this->teamRepository->updatePermissionRoles($request->permissions, $team);

        return response()->json(['message' => 'Team Role Permissions have been successfully updated.']);
    }

    /**
     * @param TeamAvailabilityRequest $request
     * @param Team                    $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/chat/status",
     *      tags={"Chat"},
     *      summary="Update team chat status.",
     *      operationId="api.chat.status.set",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *           name="Authorization",
     *           in="header",
     *           required=true,
     *           type="string",
     *           default="Bearer <token>",
     *           description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *           name="team_id",
     *           in="path",
     *           required=true,
     *           type="string"
     *      ),
     *      @SWG\Parameter(
     *           name="body",
     *           in="body",
     *           required=true,
     *           @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="away", type="boolean"),
     *           )
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function setAvailability(TeamAvailabilityRequest $request, Team $team)
    {
        $this->authorize('member', $team);

        $this->teamRepository->updateAvailability($request->available, $team);

        return response()->json(['message' => 'Team availability has been successfully updated.']);
    }

    /**
     * Get team app keys
     *
     * @param Team $team
     * @return AppKeyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/app-key",
     *      tags={"Teams"},
     *      summary="Get team app keys",
     *      operationId="api.teams.app_key",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="public_key", type="string"),
     *            @SWG\Property(property="private_key", type="string"),
     *            @SWG\Property(property="active", type="boolean"),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function getAppKeys(Team $team)
    {
        $this->authorize('manageSettings', $team);

        return new AppKeyResource($team->api_key);
    }

    /**
     * @param Team $team
     * @return AppKeyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function generateAppKeys(Team $team)
    {
        $this->authorize('manageSettings', $team);

        Artisan::call("apikey:generate --team={$team->slug}");

        return response()->json(['message' => 'Team api keys have been successfully generated.']);
    }

    /**
     * Update team plan
     *
     * @param TeamUpdatePlanRequest $request
     * @param Team                  $team
     * @param UserRepositoryInterface   $userRepository
     * @return void
     */
    public function updatePlan(
        TeamUpdatePlanRequest $request,
        Team $team,
        UserRepositoryInterface $userRepository
    )
    {
        $this->authorize('owns', $team);

        $user = auth()->user();
        if (!$user->stripe_card) {
            $userRepository->attachCard($request->card_token, $user);
        }

        $this->teamRepository->subscribeToPlan($team, $request->plan, $request->coupon);

        return response()->json(['message' => 'Team plan has been successfully changed.']);
    }

    /**
     * Delete team
     *
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     *
     * @SWG\Delete(
     *      path="/teams/{team_id}",
     *      tags={"Teams"},
     *      summary="Delete team",
     *      operationId="api.teams.delete",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function delete(Team $team)
    {
        $this->authorize('owns', $team);

        if ($team->delete()) {
            $team->invoices()->delete();
            $this->teamRepository->deleteFiles($team);
        };

        return response()->json(['message' => 'Team has been successfully deleted.']);
    }
}
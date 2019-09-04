<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Conversation;
use App\Contact;
use App\ContactTag;
use App\Http\Requests\ContactFiltersRequest;
use App\Http\Requests\ContactMultipleIdsRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactsResource;
use App\Team;
use App\Repositories\ContactRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection as AnonymousResourceCollectionAlias;

class ContactsController extends Controller
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * ContactsController constructor.
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param ContactFiltersRequest $request
     * @param Team                  $team
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(ContactFiltersRequest $request, Team $team)
    {
        $this->authorize('member', $team);

        $filters = $request->only([
            'limit',
            'search_query',
            'tags'
        ]);

        $contacts = $this->contactRepository->getList($filters);
        return view('contacts.index', compact('contacts'));
    }

    /**
     * @param Request  $request
     * @param Team $team
     * @return AnonymousResourceCollectionAlias
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/conversation-contacts",
     *      tags={"Contacts"},
     *      summary="Get contacts by latest conversations",
     *      operationId="api.contacts.conversation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
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
     *      @SWG\Parameter(
     *          type="integer",
     *          name="limit",
     *          in="query",
     *          required=false,
     *          default=10,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="array",
     *            @SWG\Items(
     *              @SWG\Property(property="id", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="avatar", type="string"),
     *              @SWG\Property(property="legal_name", type="string"),
     *              @SWG\Property(property="message_sent_at", type="string"),
     *              @SWG\Property(property="created_at", type="string"),
     *              @SWG\Property(property="updated_at", type="string"),
     *            ),
     *          ),
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
    public function conversationContacts(Request $request, Team $team)
    {
        // todo: most likely this endpoint will become useless after payment changes
        $this->authorize('member', $team);

        $conversationContactIds = Conversation::where('folder', 'open')
            ->orderByDesc('message_sent_at')
            ->take($request->limit ?? 10)
            ->pluck('contact_id')->toArray();

        $contacts = Contact::whereIn('id', $conversationContactIds)
            ->with('conversation')->get()
            ->sortByDesc('conversation.message_sent_at');

        return ContactsResource::collection($contacts);
    }

    /**
     * Get contact
     *
     * @param Team $team
     * @param Contact  $contact
     * @return ContactResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/contacts/{contact_id}",
     *      tags={"Contacts"},
     *      summary="Get contact",
     *      operationId="api.contacts.get",
     *      produces={"application/json"},
     *      @SWG\Parameter(
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
     *          name="contact_id",
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
     *            @SWG\Property(property="phone", type="string"),
     *            @SWG\Property(property="platform", type="string"),
     *            @SWG\Property(property="name", type="string"),
     *            @SWG\Property(property="legal_name", type="string"),
     *            @SWG\Property(property="avatar", type="string"),
     *            @SWG\Property(property="subscribed", type="boolean"),
     *            @SWG\Property(property="contacted", type="boolean"),
     *            @SWG\Property(
     *                 property="address",
     *                 type="object",
     *                 @SWG\Property(property="city", type="string"),
     *                 @SWG\Property(property="line", type="string"),
     *                 @SWG\Property(property="postal_code", type="string"),
     *                 @SWG\Property(property="state", type="string"),
     *                 @SWG\Property(
     *                     property="country",
     *                     type="object",
     *                     @SWG\Property(property="name", type="string"),
     *                     @SWG\Property(property="code", type="string"),
     *                 ),
     *            ),
     *            @SWG\Property(
     *               property="meta_data",
     *               type="object",
     *               @SWG\Property(property="facebook_link", type="string"),
     *               @SWG\Property(property="twitter_link", type="string"),*
     *               @SWG\Property(
     *                  property="custom_fields",
     *                  type="array",
     *                  @SWG\Items(
     *                    @SWG\Property(property="field", type="string"),
     *                  ),
     *               ),
     *               @SWG\Property(
     *                  property="notes",
     *                  type="array",
     *                  @SWG\Items(
     *                    @SWG\Property(property="note", type="string"),
     *                  ),
     *               ),
     *               @SWG\Property(
     *                    property="country",
     *                    type="object",
     *                    @SWG\Property(property="name", type="string"),
     *                    @SWG\Property(property="code", type="string"),
     *               ),
     *            ),
     *            @SWG\Property(
     *               property="tags",
     *               type="array",
     *               @SWG\Items(
     *                 @SWG\Property(property="id", type="string"),
     *                 @SWG\Property(property="name", type="string"),
     *               ),
     *            ),
     *            @SWG\Property(
     *               property="lists",
     *               type="array",
     *               @SWG\Items(
     *                 @SWG\Property(property="id", type="string"),
     *                 @SWG\Property(property="name", type="string"),
     *                 @SWG\Property(property="default", type="string"),
     *               ),
     *            ),
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
    public function show(Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        return new ContactResource($contact);
    }

    /**
     * @param ContactUpdateRequest $request
     * @param Team             $team
     * @param Contact              $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/contacts/{contact_id}",
     *      tags={"Contacts"},
     *      summary="Update contact",
     *      operationId="api.contacts.update",
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
     *      @SWG\Parameter(
     *          name="contact_id",
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
     *            @SWG\Property(property="facebook_link", type="string"),
     *            @SWG\Property(property="twitter_link", type="string"),
     *            @SWG\Property(
     *              property="address",
     *              type="object",
     *              @SWG\Property(property="city", type="string"),
     *              @SWG\Property(property="line", type="string"),
     *              @SWG\Property(property="postal_code", type="string"),
     *              @SWG\Property(property="state", type="string"),
     *              @SWG\Property(property="country", type="string"),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          description="Bad Request"
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
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function update(ContactUpdateRequest $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $this->contactRepository->update($request->all(), $contact);

        return response()->json(['message' => 'Contact data has been successfully updated.']);
    }

    /**
     * @param Request  $request
     * @param Team $team
     * @param Contact  $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/contacts/{contact_id}/sync-notes",
     *      tags={"Contacts"},
     *      summary="Sync contact notes",
     *      operationId="api.contacts.sync_notes",
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
     *      @SWG\Parameter(
     *          name="contact_id",
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
     *            @SWG\Property(
     *              property="notes",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="note", type="string"),
     *              ),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          description="Bad Request"
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
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function syncNotes(Request $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $contact->data()->update([
            'notes' => json_encode($request->notes),
        ]);

        return response()->json(['message' => 'Contact notes have been successfully updated.']);
    }

    /**
     * @param Request  $request
     * @param Team $team
     * @param Contact  $contact
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/contacts/{contact_id}/sync-custom-fields",
     *      tags={"Contacts"},
     *      summary="Sync contact custom fields",
     *      operationId="api.contacts.sync_custom_fields",
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
     *      @SWG\Parameter(
     *          name="contact_id",
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
     *            @SWG\Property(
     *              property="custom_fields",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="field", type="string"),
     *              ),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          description="Bad Request"
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
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function syncCustomFields(Request $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $customFields = contact_fields($team);
        $customFieldSlugs = $customFields->pluck('slug')->toArray();
        $customData = array_leave_only($request->custom_fields, $customFieldSlugs);
        $this->contactRepository->validateCustomFields($customFields, $customData);

        $contact->data()->update([
            'custom_fields' => json_encode($customData),
        ]);

        return response()->json(['message' => 'Contact custom fields have been successfully updated.']);
    }

    /**
     * @param Request  $request
     * @param Team $team
     * @param Contact  $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Put(
     *      path="/teams/{team_id}/contacts/{contact_id}/sync-tags",
     *      tags={"Contacts"},
     *      summary="Sync contact tags",
     *      operationId="api.contacts.sync_tags",
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
     *      @SWG\Parameter(
     *          name="contact_id",
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
     *            @SWG\Property(
     *              property="tags",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="id", type="string"),
     *              ),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          description="Bad Request"
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
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function syncTags(Request $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $tagIds = ContactTag::whereIn('uid', $request->tags)->pluck('id')->toArray();

        $contact->tags()->sync($tagIds);

        return response()->json(['message' => 'Contact attached tags have been successfully updated.']);
    }

    /**
     * @param Request  $request
     * @param Team $team
     * @param Contact  $contact
     * @return AnonymousResourceCollectionAlias
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *     path="/teams/{team_id}/contacts/{contact_id}/activities",
     *     tags={"Contacts"},
     *     operationId="api.contacts.activities.get",
     *     summary="Get contact activities.",
     *     produces={"application/json"},
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
     *          name="contact_id",
     *          in="query",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="limit",
     *          in="query",
     *          required=false,
     *          default=10,
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="page",
     *          in="query",
     *          required=false,
     *          default=1,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            @SWG\Property(
     *              property="data",
     *              type="array",
     *              @SWG\Items(
     *                @SWG\Property(property="type", type="string"),
     *                @SWG\Property(property="description", type="string"),
     *                @SWG\Property(property="meta_data", type="string"),
     *                @SWG\Property(property="happened_at", type="string"),
     *              ),
     *            ),
     *            @SWG\Property(
     *               property="links",
     *               type="object",
     *               @SWG\Property(property="first", type="string"),
     *               @SWG\Property(property="last", type="string"),
     *               @SWG\Property(property="prev", type="string"),
     *               @SWG\Property(property="next", type="string"),
     *            ),
     *            @SWG\Property(
     *               property="meta",
     *               type="object",
     *               @SWG\Property(property="current_page", type="integer"),
     *               @SWG\Property(property="from", type="integer"),
     *               @SWG\Property(property="last_page", type="integer"),
     *               @SWG\Property(property="path", type="string"),
     *               @SWG\Property(property="per_page", type="integer"),
     *               @SWG\Property(property="to", type="integer"),
     *               @SWG\Property(property="total", type="integer"),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     * )
     */
    public function getActivities(Request $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $filters = $request->only(['limit', 'page']);
        $activities = $contact->activities()->with([
            'user',
            'contact_list'
        ])
            ->whereNotIn('type', ['daily_contacted'])
            ->orderByDesc('happened_at')
            ->orderByDesc('id')->paginate($filters['limit'] ?? 10);

        return ActivityResource::collection($activities);
    }

    /**
     * @param Team $team
     * @param Contact  $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Delete(
     *      path="/teams/{team_id}/contacts/{contact_id}",
     *      tags={"Contacts"},
     *      summary="Delete contact",
     *      operationId="api.contacts.delete",
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
     *          name="contact_id",
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
    public function delete(Team $team, Contact $contact)
    {
        $this->authorize('manageContacts', $team);

        $contact->conversation->delete();
        $contact->delete();

        return response()->json(['message' => 'Contact has been successfully deleted.']);
    }

    /**
     * @param ContactMultipleIdsRequest $request
     * @param Team                  $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Delete(
     *      path="/teams/{team_id}/contacts",
     *      tags={"Contacts"},
     *      summary="Delete multiple contacts",
     *      operationId="api.contacts.delete-multiple",
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
     *          name="contact_ids",
     *          in="query",
     *          required=true,
     *          type="array",
     *          items="string",
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
    public function deleteMultiple(ContactMultipleIdsRequest $request, Team $team)
    {
        $this->authorize('manageContacts', $team);

        $contactIds = Contact::whereIn('uid', $request->contact_ids)->pluck('id')->toArray();
        Conversation::whereIn('contact_id', $contactIds)->delete();
        Contact::whereIn('id', $contactIds)->delete();

        return response()->json(['message' => 'Contacts have been successfully deleted.']);
    }
}

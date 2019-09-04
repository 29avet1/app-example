<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Http\Requests\ContactFiltersRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactsResource;
use App\Repositories\ContactRepository;
use App\Team;
use Illuminate\Http\Request;

class ContactsController extends ApiController
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
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

        return ContactsResource::collection($contacts);
    }

    /**
     * Get contact
     *
     * @param Team    $team
     * @param Contact $contact
     * @return ContactResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        return new ContactResource($contact);
    }

    /**
     * @param ContactUpdateRequest $request
     * @param Team                 $team
     * @param Contact              $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     */
    public function update(ContactUpdateRequest $request, Team $team, Contact $contact)
    {
        $this->authorize('member', $team);

        $this->contactRepository->update($request->all(), $contact);

        return response()->json(['message' => 'Contact data has been successfully updated.']);
    }

    /**
     * @param Team    $team
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     */
    public function delete(Team $team, Contact $contact)
    {
        $this->authorize('manageContacts', $team);

        $contact->conversation->delete();
        $contact->delete();

        return response()->json(['message' => 'Contact has been successfully deleted.']);
    }

    /**
     * @param Request $request
     * @param Team    $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteMultiple(Request $request, Team $team)
    {
        $this->authorize('manageContacts', $team);

        $contactIds = Contact::whereIn('uid', $request->contact_ids)->pluck('id')->toArray();
        Contact::whereIn('id', $contactIds)->delete();

        return response()->json(['message' => 'Contacts have been successfully deleted.']);
    }
}

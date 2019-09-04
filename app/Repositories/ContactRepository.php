<?php

namespace App\Repositories;

use App\Contact;
use App\ContactList;
use App\Contracts\Repositories\ContactActivityRepositoryInterface;
use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Http\Requests\ContactListSubscribeRequest;
use App\Team;
use App\MessagingPlatform;
use App\Services\Smooch\WhatsApp\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;
use Ramsey\Uuid\Uuid;

class ContactRepository extends Repository implements ContactRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Contact::class;
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getList(array $filters): LengthAwarePaginator
    {
        $contacts = Contact::with([
            'address',
            'tags',
            'lists'        => function ($query) {
                $query->select(['contact_lists.id', 'contact_lists.uid', 'name']);
            },
            'conversation' => function ($query) {
                $query->select(['conversations.id', 'contact_id', 'folder']);
            },
        ]);

        // todo: change search query once we enable algolia
        if (@$filters['search_query'] && (trim($filters['search_query']) !== '')) {
            $contacts
                ->where('phone', 'like', '%' . $filters['search_query'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search_query'] . '%')
                ->orWhere(DB::raw('lower(name)'), 'like', '%' . strtolower($filters['search_query']) . '%');
        }

        if (@$filters['tags'] && !empty($filters['tags'])) {
            $tagUids = $filters['tags'];
            $contacts->whereHas('tags', function ($query) use ($tagUids) {
                $query->whereIn('uid', $tagUids);
            }, count($tagUids));
        }

        return $contacts
            ->orderByDesc('created_at')
            ->paginate($filters['limit'] ?? 25);
    }

    /**
     * @param $contactData
     * @param $team
     * @param $MessagingPlatform
     * @return Contact
     * @throws \Exception
     */
    public function sync(array $contactData, Team $team, MessagingPlatform $MessagingPlatform): Contact
    {
        /** @var Contact $contact */
        $contact = Contact::firstOrCreate([
            'team_id' => $team->id,
            'platform_id' => $MessagingPlatform->id,
            'phone'       => PhoneNumber::make($contactData['phone'])->formatE164(),
        ], [
            'name'   => @$contactData['name'],
            'avatar' => random_int(1, 5),
            'uid'    => Uuid::uuid4(),
        ]);

        return $contact;
    }

    /**
     * @param array   $contactData
     * @param Contact $contact
     */
    public function update(array $contactData, Contact $contact): void
    {
        $contact->name = $contactData['name'];
        $contact->email = $contactData['email'];
        $contact->save();

        if (@$contactData['address'] && !empty($contactData['address'])) {
            if ($address = $contact->address) {
                $address->update($contactData['address']);
            } else {
                $contact->address()->create($contactData['address']);
            }
        }

        if (@$contactData['facebook_link'] || @$contactData['twitter_link']) {
            $contact->data()->update(array_only($contactData, [
                'facebook_link',
                'twitter_link',
            ]));
        }
    }

    /**
     * @param Carbon $endDay
     * @param array  $dateIntervals
     * @return array
     */
    public function getAnalytics(Carbon $endDay, array $dateIntervals): array
    {
        $selectCount = [];
        foreach ($dateIntervals as $dateInterval) {
            $timestamp = $dateInterval['start']->timestamp;
            $startInterval = $dateInterval['start']->setTimezone('UTC');
            $endInterval = $dateInterval['end']->setTimezone('UTC');
            $selectCount[] = DB::raw("count(CASE WHEN created_at <= '{$endInterval}' AND deleted_at IS null THEN id ELSE null END) as all_count_{$timestamp}");
            $selectCount[] = DB::raw("count(CASE WHEN created_at >= '{$startInterval}' AND created_at <= '{$endInterval}' AND deleted_at IS null THEN id ELSE null END) as new_count_{$timestamp}");
        }

        $contacts = DB::table('contacts')->select($selectCount)->first();

        $newContacts = [];
        $allContacts = [];
        foreach ($dateIntervals as $dateInterval) {
            $timestamp = $dateInterval['start']->timestamp;
            $allContacts[$timestamp] = $contacts->{"all_count_{$timestamp}"};
            $newContacts[$timestamp] = $contacts->{"new_count_{$timestamp}"};
        }

        $totalContacts = Contact::where('created_at', '<=', $endDay)->count();

        return [
            'total' => $totalContacts,
            'new'   => $newContacts,
            'all'   => $allContacts,
        ];
    }
}
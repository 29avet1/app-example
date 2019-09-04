<?php

namespace App;

use App\Traits\Models\UsesUuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @property integer         $id
 * @property string          $uuid
 * @property integer         $team_id
 * @property string          $phone
 * @property string          $email
 * @property string         $name
 * @property string         $avatar
 * @property string         $legal_name
 * @property string         $conversation_status
 * @property string         $subscribed_via
 * @property bool           $contacted
 * @property bool           $subscribed
 * @property array          $meta_data
 * @property Carbon         $created_at
 * @property Carbon         $updated_at
 * @property Carbon         $unsubscribed_at
 * @property Collection     $activities
 * @property Collection     $lists
 * @mixin \Eloquent
 * @package App
 */
class Contact extends Model
{
    use UsesUuids, SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'uuid',
        'team_id',
        'phone',
        'email',
        'name',
        'avatar',
        'unsubscribed_at',
        'meta_data',
    ];

    protected $appends = [
        'legal_name',
        'subscribed',
        'conversation_status',
    ];

    protected $dates = [
        'unsubscribed_at',
    ];

    protected $casts = [
        'meta_data' => 'json',
    ];

    //-----Attributes-------------------------------------------------------------------------------------------------//

    /**
     * @return bool
     */
    public function getSubscribedAttribute()
    {
        return !$this->unsubscribed_at;
    }

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}

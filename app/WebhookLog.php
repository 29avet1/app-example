<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WebhookLog
 *
 * @property integer         $id
 * @property string          $uuid
 * @property integer         $team_id
 * @property integer         $endpoint_id
 * @property integer         $response_status_code
 * @property string          $type
 * @property string          $request_body
 * @property string          $response_body
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property Team            $team
 * @property WebhookEndpoint $endpoint
 * @package App
 *
 * @mixin \Eloquent
 */
class WebhookLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'endpoint_id',
        'response_status_code',
        'type',
        'request_body',
        'response_body',
    ];

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function endpoint()
    {
        return $this->belongsTo(WebhookEndpoint::class, 'endpoint_id', 'id');
    }
}

<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WebhookEndpoint
 *
 * @property integer         $id
 * @property integer         $team_id
 * @property integer         $endpoint_id
 * @property string          $type
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 * @property Team            $team
 * @property WebhookEndpoint $endpoint
 * @package App
 *
 * @mixin \Eloquent
 */
class SelectedWebhook extends Model
{
    protected $table = 'selected_webhooks';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'team_id',
        'endpoint_id',
        'type',
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

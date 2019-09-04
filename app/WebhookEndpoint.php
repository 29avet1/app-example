<?php

namespace App;

use App\Traits\Models\UsesUuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WebhookEndpoint
 *
 * @property integer    $id
 * @property string     $uuid
 * @property integer    $team_id
 * @property string     $url
 * @property string     $secret_key
 * @property bool       $active
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Team       $team
 * @property Collection $selected_webhooks
 * @property Collection $webhook_logs
 * @package App
 *
 * @mixin \Eloquent
 */
class WebhookEndpoint extends Model
{
    use UsesUuids;

    protected $fillable = [
        'uuid',
        'team_id',
        'url',
        'secret_key',
        'active',
    ];

    //-----Attributes-------------------------------------------------------------------------------------------------//

    /**
     * @param $key
     * @return string
     */
    public function getSecretKeyAttribute($key)
    {
        return decrypt($key);
    }

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selected_webhooks()
    {
        return $this->hasMany(SelectedWebhook::class, 'endpoint_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webhook_logs()
    {
        return $this->hasMany(WebhookLog::class, 'endpoint_id', 'id');
    }
}

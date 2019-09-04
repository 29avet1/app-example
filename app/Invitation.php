<?php

namespace App;

use App\Traits\Models\UsesUuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invitation
 * @property integer  $id
 * @property string   $uid
 * @property integer  $user_id
 * @property integer  $team_id
 * @property integer  $role_id
 * @property string   $email
 * @property string   $token
 * @property Carbon   $expires_at
 * @property Carbon   $created_at
 * @property Carbon   $updated_at
 * @property User     $user
 * @property Role     $role
 * @property Team $team
 * @mixin \Eloquent
 * @package App
 */
class Invitation extends Model
{
    protected $table = 'invitations';

    use UsesUuids;

    protected $fillable = [
        'uid',
        'user_id',
        'team_id',
        'role_id',
        'email',
        'token',
        'expires_at'
    ];

    protected $casts = [
        'team_ids' => 'json',
    ];

    protected $dates = [
        'expires_at'
    ];


    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}

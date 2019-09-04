<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class RoleUserPivot
 * @property integer $role_id
 * @property integer $user_id
 * @property integer $team_id
 * @property Role $role
 * @property Team $team
 * @property User $user
 * @mixin \Eloquent
 * @package App
 */
class RoleUserPivot extends Model
{
    protected $table = 'role_user';
    public $incrementing = false;
    public $timestamps = false;

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

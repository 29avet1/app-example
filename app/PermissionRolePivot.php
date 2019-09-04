<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionRolePivot
 * @property integer $role_id
 * @property integer $user_id
 * @property integer $team_id
 * @property Role $role
 * @property Team $team
 * @property Permission $Permission
 * @mixin \Eloquent
 * @package App
 */
class PermissionRolePivot extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
        'team_id',
    ];

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
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}

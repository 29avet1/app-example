<?php

namespace App;

use App\Traits\Models\RolesTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use RolesTrait;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public static $roleNames = [
        'admin',
        'team_lead',
        'member'
    ];

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'role_user', 'role_id', 'team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
}
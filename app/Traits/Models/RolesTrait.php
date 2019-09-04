<?php

namespace App\Traits\Models;

use App\PermissionRolePivot;
use App\Team;
use Illuminate\Support\Facades\Cache;
use Laratrust\Traits\LaratrustRoleTrait;

trait RolesTrait
{
    use LaratrustRoleTrait;

    /**
     * Save the inputted permissions.
     *
     * @param mixed $permissions
     * @param Team  $team
     * @return RolesTrait
     */
    public function syncPermissions($permissions, Team $team)
    {
        $mappedPermissions = [];

        foreach ($permissions as $permissionId) {
            $mappedPermissions[] = [
                'role_id'       => $this->id,
                'permission_id' => $permissionId,
                'team_id'       => $team->id,
            ];
        }

        PermissionRolePivot::where('role_id', $this->id)->where('team_id', $team->id)->delete();
        PermissionRolePivot::insert($mappedPermissions);
        $this->flushCache($team);

        return $this;
    }

    /**
     * @param          $permission
     * @param Team     $team
     * @return bool
     * @throws \Exception
     */
    public function hasPermission($permission, Team $team)
    {
        foreach ($this->cachedPermissions($team) as $perm) {
            if (str_is($permission, $perm['name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Team $team
     * @return \Illuminate\Cache\CacheManager|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Exception
     */
    protected function cachedPermissions(Team $team)
    {
        if (!config('laratrust.use_cache')) {
            return $this->permissions()->wherePivot('team_id', $team->id)->get();
        }

        return Cache::remember($this->getPermissionCacheKey($team), config('cache.ttl', 3600),
            function () use ($team) {
                return $this->permissions()
                    ->wherePivot('team_id', $team->id)
                    ->get();
            });
    }

    /**
     * @param Team $team
     * @return string
     */
    protected function getPermissionCacheKey(Team $team)
    {
        return "permissions_for_role_{$this->id}_m_{$team->id}";
    }

    /**
     * @param Team $team
     * @throws \Exception
     */
    protected function flushCache(Team $team)
    {
        Cache::forget($this->getPermissionCacheKey($team));
    }
}
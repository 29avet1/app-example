<?php

namespace App\Traits\Models;

use App\team;
use App\Role;
use Illuminate\Support\Facades\Cache;

trait RolesUserTrait
{
    /**
     * Sync roles to the user.
     *
     * @param Role    $role
     * @param Team    $team
     * @param array   $fields
     * @param boolean $detaching
     * @return static
     * @throws \Exception
     */
    public function updateRole(Role $role, Team $team, $fields = [], $detaching = true)
    {
        $mappedObjects = [];

        $mappedObjects[$role->id] = array_merge(['team_id' => $team->id], $fields);

        $this->roles()
            ->wherePivot('team_id', $team->id)
            ->sync($mappedObjects, $detaching);

        $this->flushCache($team);

        return $this;
    }

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param Role  $role
     * @param team  $team
     * @param array $fields
     * @return static
     * @throws \Exception
     */
    public function attachRole(Role $role, team $team, $fields = [])
    {
        if (
        $this->roles()
            ->wherePivot('team_id', $team->id)
            ->wherePivot('role_id', $role->id)
            ->count()
        ) {
            return $this;
        }

        $attributes = array_merge(['team_id' => $team->id], $fields);
        $this->roles()->attach($role->id, $attributes);
        $this->flushCache($team);

        return $this;
    }

    /**
     * @param array $roles
     * @param team  $team
     * @param array $fields
     * @param bool  $detaching
     * @return $this
     * @throws \Exception
     */
    public function syncRoles($roles = [], team $team, $fields = [], $detaching = true)
    {
        $mappedRoles = [];

        foreach ($roles as $roleId) {
            $mappedRoles[$roleId] = ['team_id' => $team->id];
        }

        $this->roles()->sync($mappedRoles, $detaching);
        $this->flushCache($team);

        return $this;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param team         $team name or requiredAll roles.
     * @return bool
     * @throws \Exception
     */
    public function hasPermission($permission, team $team)
    {
        foreach ($this->cachedRoles($team) as $role) {
            if ($role->hasPermission($permission, $team)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|null $role .
     * @param team        $team name or requiredAll roles.
     * @return bool
     * @throws \Exception
     */
    public function hasRole(string $role = null, team $team)
    {
        $roles = $this->cachedRoles($team);

        // check if user has any role in this team
        if (!$role && $roles->count()) {
            return true;
        }

        return (bool)$roles->where('name', $role)->first();
    }

    /**
     * @param team $team
     * @return string
     */
    protected function getRoleCacheKey(team $team)
    {
        return "roles_for_user_{$this->id}_m_{$team->id}";
    }

    /**
     * @param team $team
     * @return \Illuminate\Cache\CacheManager|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Exception
     */
    protected function cachedRoles(team $team)
    {
        if (!config('laratrust.use_cache')) {
            return $this->roles()->wherePivot('team_id', $team->id)->get();
        }

        return Cache::remember($this->getRoleCacheKey($team), config('cache.ttl', 3600),
            function () use ($team) {
                return $this->roles()
                    ->wherePivot('team_id', $team->id)
                    ->get();
            });
    }

    /**
     * @param team $team
     * @throws \Exception
     */
    protected function flushCache(team $team)
    {
        Cache::forget($this->getRoleCacheKey($team));
    }
}
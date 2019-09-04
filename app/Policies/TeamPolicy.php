<?php

namespace App\Policies;

use App\Team;
use App\User;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * @param User   $user
     * @param string $ability
     * @param Team   $team
     * @return bool
     * @throws Exception
     */
    public function before(User $user, $ability, Team $team)
    {
        if ($user->hasRole('owner', $team)) {
            return true;
        }
    }

    /**
     * @param User $user
     * @param Team $team
     * @return bool
     * @throws Exception
     */
    public function manageSettings(User $user, Team $team)
    {
        if ($user->hasPermission('manage_settings', $team)) {
            return true;
        }

        $this->deny('You don\'t have permission to manage this team.');
    }

    /**
     * @param User $user
     * @param Team $team
     * @return bool
     * @throws Exception
     */
    public function manageContacts(User $user, Team $team)
    {
        if ($user->hasPermission('manage_contacts', $team)) {
            return true;
        }

        $this->deny('You don\'t have permission to manage contacts.');
    }

    /**
     * @param User $user
     * @param Team $team
     * @return bool
     * @throws Exception
     */
    public function manageInvoices(User $user, Team $team)
    {
        if ($user->hasPermission('manage_invoices', $team)) {
            return true;
        }

        $this->deny('You don\'t have permission to manage invoices.');
    }

    /**
     * @param User $user
     * @param Team $team
     * @return bool
     * @throws Exception
     */
    public function viewInvoices(User $user, Team $team)
    {
        if ($user->hasPermission('view_invoices', $team)) {
            return true;
        }

        $this->deny('You don\'t have permission to view invoices.');
    }

    /**
     * @param User $user
     * @param Team $team
     * @throws Exception
     */
    public function owns(User $user, Team $team)
    {
        // don't need to check anything, cause it will be checked by 'before' method

        $this->deny('You don\'t have owner permission to do this action.');
    }

    /**
     * @param User $user
     * @param Team $team
     * @return bool
     * @throws Exception
     */
    public function member(User $user, Team $team)
    {
        if ($user->hasRole(null, $team)) {
            return true;
        }

        $this->deny('You don\'t have any permissions in this team.');
    }
}

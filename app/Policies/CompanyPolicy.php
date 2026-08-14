<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine if the user could add a new company.
     *
     * @param  \App\User  $user
     * @param  \App\Company  $company
     * @return bool
     */
    public function store(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine if the given company can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Company  $company
     * @return bool
     */
    public function update(User $user, Company $company)
    {
        //return $user->id === $company->created_by;
        return true;
    }
}

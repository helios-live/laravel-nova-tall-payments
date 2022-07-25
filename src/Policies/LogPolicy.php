<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Policies;

use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Log;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function before(User $user, $ability)
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Log $log)
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user, Log $log)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Log $log)
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Log $log)
    {
        return $user->isSuperAdmin();
    }
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
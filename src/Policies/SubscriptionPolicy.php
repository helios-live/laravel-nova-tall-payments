<?php

namespace AlexEftimie\LaravelPayments\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use AlexEftimie\LaravelPayments\Models\Subscription;

class SubscriptionPolicy
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
        return true;
    }

    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function view(User $user, Subscription $subscription)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Subscription $subscription)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Subscription $subscription)
    {
        return $user->isAdmin();
    }

    public function manage(User $user, Subscription $subscription)
    {
        $team = $subscription->owner;

        if (!$user->hasTeamPermission($team, 'subscription:manage')) {
            return false;
        }
        return $user->belongsToTeam($team);
    }
}

<?php

namespace AlexEftimie\LaravelPayments\Policies;

use App\Models\User;
use AlexEftimie\LaravelPayments\Models\Price;
use AlexEftimie\LaravelPayments\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class PricePolicy
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
        return $user->isAdmin();
    }

    // public function before(User $user, $ability)
    // {
    //     return true;
    // }

    public function view(User $user, Price $price)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Price $price)
    {
        return true;
    }

    public function delete(User $user, Price $price)
    {
        if ($price->subscriptions()->count() > 0) {
            return false;
        }
        return true;
    }
}

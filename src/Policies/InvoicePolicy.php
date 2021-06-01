<?php

namespace AlexEftimie\LaravelPayments\Policies;

use AlexEftimie\LaravelPayments\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
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

    public function refund(User $user)
    {
        return $user->isAdmin();
    }

    public function pay(User $user, Invoice $invoice)
    {
        $team = $invoice->owner;
        if (!$user->hasTeamPermission($team, 'billing:pay')) {
            return false;
        }
        return $user->belongsToTeam($team);
    }

    public function payManual(User $user)
    {
        return $user->isAdmin();
    }
}
<?php

namespace IdeaToCode\LaravelNovaTallPayments\Policies;

use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
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
        if (!$user->belongsToTeam($team)) {
            return false;
        }
        if (!$user->hasTeamPermission($team, 'billing:pay')) {
            return false;
        }
        return true;
    }

    public function download(User $user, Invoice $invoice)
    {
        $team = $invoice->owner;
        if (!$user->belongsToTeam($team)) {
            return false;
        }
        if (!$user->hasTeamPermission($team, 'billing:pay')) {
            return false;
        }
        return true;
    }

    public function payManual(User $user)
    {
        return $user->isAdmin();
    }
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function view(User $user, Invoice $subscription)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Invoice $subscription)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Invoice $subscription)
    {
        return $user->isAdmin();
    }
}

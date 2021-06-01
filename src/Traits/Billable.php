<?php

namespace AlexEftimie\LaravelPayments\Traits;

use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Models\Commission;
use AlexEftimie\LaravelPayments\Models\Subscription;
use AlexEftimie\LaravelPayments\Models\CommissionPayment;

trait Billable {
    public function getName() {
        if ( isset($this->name) ) {
            return $this->name;
        }

        return get_class($this) . ' ' . $this->getKey();
    }

    public function getEmail() {

        // if the team has an email
        if ( isset($this->email) ) {
            return $this->email;
        }

        // if the team owner has an email
        if ( isset($this->owner) && isset($this->owner->email) )
        {
            return $this->owner->email;
        }

        return null;
    }
    public function invoices() {
        return $this->morphMany(Invoice::class, 'owner')->orderBy('id', 'desc');
    }
    public function subscriptions() {
        return $this->morphMany(Subscription::class, 'owner');
    }
    public function commissions() {
        return $this->morphMany(Commission::class, 'owner');
    }
    public function commissionPayments() {
        return $this->morphMany(CommissionPayment::class, 'owner');
    }
}
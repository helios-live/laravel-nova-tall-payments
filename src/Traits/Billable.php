<?php

namespace AlexEftimie\LaravelPayments\Traits;

use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Models\Commission;
use AlexEftimie\LaravelPayments\Models\Subscription;
use AlexEftimie\LaravelPayments\Models\CommissionPayment;

trait Billable
{
    public function getName()
    {
        if (isset($this->name)) {
            return $this->name;
        }

        return get_class($this) . ' ' . $this->getKey();
    }

    public function getEmail()
    {

        // if the team has an email
        if (isset($this->email)) {
            return $this->email;
        }

        // if the team owner has an email
        if (isset($this->owner) && isset($this->owner->email)) {
            return $this->owner->email;
        }

        return null;
    }

    public function hasSetUpBilling(): bool
    {
        return !is_null($this->billing_name)
            && !is_null($this->billing_address)
            && !is_null($this->billing_country);
    }

    public function getBillingName()
    {
        return $this->billing_name;
    }

    public function getBillingAddress()
    {
        return $this->billing_address;
    }
    public function getBillingCountry()
    {
        return $this->billing_country;
    }

    public function getBillingCode()
    {
        return $this->billing_code;
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'owner')->orderBy('id', 'desc');
    }
    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'owner');
    }
    public function commissions()
    {
        return $this->morphMany(Commission::class, 'owner');
    }
    public function commissionPayments()
    {
        return $this->morphMany(CommissionPayment::class, 'owner');
    }
}

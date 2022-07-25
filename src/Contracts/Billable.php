<?php

namespace IdeaToCode\LaravelNovaTallPayments\Contracts;


interface Billable
{
    public function getKey();
    public function getName();
    public function getEmail();
    public function invoices();
    public function subscriptions();

    // invoice info
    public function getBillingName();
    public function getBillingCode();
    public function getBillingAddress();
    public function getBillingCountry();
    public function hasSetUpBilling(): bool;
}
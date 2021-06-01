<?php

namespace AlexEftimie\LaravelPayments\Contracts;


interface Billable
{
    public function getKey();
    public function getName();
    public function getEmail();
    public function invoices();
    public function subscriptions();
}
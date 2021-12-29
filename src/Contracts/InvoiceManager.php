<?php

namespace AlexEftimie\LaravelPayments\Contracts;

interface InvoiceManager
{
    public function isBillingSetUp();
    public function downloadRoute();
}

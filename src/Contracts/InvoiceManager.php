<?php

namespace IdeaToCode\LaravelNovaTallPayments\Contracts;

interface InvoiceManager
{
    public function isBillingSetUp();
    public function downloadRoute();
}

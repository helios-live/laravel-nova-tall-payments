<?php

namespace IdeaToCode\LaravelNovaTallPayments\Payments;

use Illuminate\Http\Request;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;

interface PaymentGatewayInterface
{
    public function createCustomer(Invoice $invoice, $payload);
    public function createSingleCharge(Invoice $invoice, $payload);
    public function charge(Invoice $invoice, $payload);
    public function refund(Invoice $invoice, $eid);
    public function getPaymentModalView();
}
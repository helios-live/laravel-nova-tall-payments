<?php

namespace AlexEftimie\LaravelPayments\Payments;

use Illuminate\Http\Request;
use AlexEftimie\LaravelPayments\Models\Invoice;

interface PaymentGatewayInterface {
    public function createCustomer(Invoice $invoice, $payload);
    public function createSingleCharge(Invoice $invoice, $payload);
    public function charge(Invoice $invoice, $payload);
    public function refund(Invoice $invoice, $eid);
    public function getPaymentModalView();
}
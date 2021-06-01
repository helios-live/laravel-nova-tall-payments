<?php

namespace AlexEftimie\LaravelPayments\Payments;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use AlexEftimie\LaravelPayments\Models\Log;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Facades\Larapay;

class ManualGateway implements PaymentGatewayInterface {

    public function createCustomer(Invoice $invoice, $payload) {
    	return (object)[
    		'id' => 0
    	];
    }
    public function createSingleCharge(Invoice $invoice, $payload){}

    public function charge($invoice, $payload) {
        if(!Gate::check('pay-manual', $invoice)) {
            Log::add($invoice->owner, 'ManualGateway::charge::failed', ['reason' => 'not-allowed', 'user' => auth()->user()]);
            return (object)[
                'status' => 'failed'
            ];
        }
        Log::add($invoice->owner, 'ManualGateway::charge::succeeded', ['user' => auth()->user()]);

    	return (object)[
            'id' => 'N/A',
    		'status' => 'succeeded'
    	];
    }

    public function refund(Invoice $invoice, $eid)
    {
        Log::add($invoice->owner, 'ManualGateway::refund::refunded', ['user' => auth()->user()]);
        return (object)[
            'id' => 'N/A',
            'status' => 'refunded'
        ];
    }
    
    public function getPaymentModalView() {
    	return [
            'larapay::manual-single', [

            ]
        ];
    }
}
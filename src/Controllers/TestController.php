<?php

namespace AlexEftimie\LaravelPayments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Facades\Larapay;
use AlexEftimie\LaravelPayments\Payments\StripeGateway;
use AlexEftimie\LaravelPayments\Errors\InvoiceAlreadyPaid;

class TestController extends Controller {

    /**
     * First you start a newFlow and you chose a payment gateway
     */
    public function newFlow(Invoice $invoice)
    {
        if ( ! is_null($invoice->paid_at) ) {
            throw new InvoiceAlreadyPaid;
        }

        $data = [
            'gateways' => Larapay::gateways(),
            'invoice' => $invoice,
        ];
        return view('larapay::choose-gateway', $data);
    }

    public function showInvoice($invoice) {
        return view('larapay::show-invoice', ['invoice' => $invoice]);
    }

    public function success(Request $request)
    {
        $invoice = Invoice::where('uuid', $request->invoice)->first();
        if ( is_null($invoice) ) {
            abort(404);
        }
        if ( !Gate::check('pay', $invoice)) {
            abort(403);
        }

        $payload = [
            'request' => $request,
        ];

        return Larapay::charge($request->gateway, $invoice, $payload);
    }

    public function cancel() {
        dd(__FILE__ . ":" . __LINE__, "Cancel");
    }

    // /**
    //  * Second you Initialize the chosen payment gateway and show the form
    //  */
    // public function gw(Request $request, string $gateway, Invoice $invoice) {

    //     $gw = Larapay::driver($gateway);
        
    //     $amount = $invoice->amount;

    //     $owner = $invoice->owner;

    //     if ( ! is_null($invoice->paid_at) ) {
    //         throw new InvoiceAlreadyPaid;
    //     }

    //     if ($request->method() == "POST") {

    //         // TODO: Save customer, retrieve existing
    //         $customer = $gw->createCustomer($owner->getName(), $owner->getEmail());

    //         $charge = $gw->createSingleCharge($amount, config('larapay.currency_code'), $invoice->getDescription(), null, $customer->id, $request);

    //         return Larapay::processResult($request, $charge);
    //     }
    //     return $gw->showForm();
    // }

}
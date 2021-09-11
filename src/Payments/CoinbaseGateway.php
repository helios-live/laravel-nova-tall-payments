<?php

namespace AlexEftimie\LaravelPayments\Payments;

use Illuminate\Http\Request;
use Shakurov\Coinbase\Facades\Coinbase;
use AlexEftimie\LaravelPayments\Models\Log;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Facades\Larapay;
use Shakurov\Coinbase\Models\CoinbaseWebhookCall;

class CoinbaseGateway implements PaymentGatewayInterface
{
    const STATUS_COMPLETED = "COMPLETED";
    const STATUS_NEW = "NEW";

    public function createSingleCharge(Invoice $invoice, $payload)
    {
        $charge = Coinbase::createCharge($data = [
            'name' => 'Name',
            'description' => $invoice->name,
            'local_price' => [
                'amount' => $invoice->amount / 100,
                'currency' => config('larapay.currency_code'),
            ],
            'metadata' => [
                'invoice' => $invoice->uuid,
            ],
            'pricing_type' => 'fixed_price',
            'redirect_url' => route('payment.success', ['gateway' => 'coinbase', 'invoice' => $invoice]),
            'cancel_url' => route('payment.cancel', ['gateway' => 'coinbase', 'invoice' => $invoice]),
        ]);

        $invoice->addOrUpdateMeta('coinbase_charge_code', $charge['data']['code']);

        return (object)[
            'status' => 'redirect',
            'redirect' => $charge['data']['hosted_url']
        ];
    }

    public function saveMethod($owner, $charge)
    {
    }

    public function callback(Request $request)
    {
        return '';
    }
    public function cancel(Invoice $invoice)
    {
        return (object)[
            'status' => 'failed',
            'message' => "Canceled by user",
        ];
    }
    public function charge(Invoice $invoice, $payload)
    {
        // invoice already paid?
        if ($invoice->status == 'paid') {
            return (object)[
                'status' => 'redirect',
                'redirect' => route('invoice.show', ['invoice' => $invoice]),
            ];
        }

        // get charge info
        $charge_code = $invoice->getMetaValue('coinbase_charge_code');
        $charge = Coinbase::getCharge($charge_code);

        $charge = CoinbaseWebhookCall::find(5)->payload['event'];


        $timeline = $charge['data']['timeline'];
        $latest_step = array_pop($timeline);


        // is invoice paid now, before postback?
        // return status succeeded and larapay will handle payment
        if ($latest_step['status'] == self::STATUS_COMPLETED) {
            return (object)[
                'status' => 'succeeded',
                'id' => $latest_step['payment']['network'] . "_" . $latest_step['payment']['transaction_id'],
            ];
        }

        // is the payment pending?
        // show the invoice
        if ($latest_step['status'] == self::STATUS_NEW) {
            return (object)[
                'status' => 'redirect',
                'redirect' => route('invoice.show', ['invoice' => $invoice]),
            ];
        }

        // do not send an email notification if the user has already seen the failure
        $invoice->addOrUpdateMeta('failure_displayed', true);

        $status = $latest_step['status'];
        return (object)[
            'status' => 'failed',
            'message' => 'Charge failed for reason: ' . $status,
        ];
    }

    public function getPaymentModalView()
    {
        return [
            'larapay::coinbase-single', []
        ];
    }
    public function getClient()
    {
    }
    public function createCustomer(Invoice $invoice, $payload)
    {
    }
    public function updatePaymentMethods($param, Invoice $invoice, $payload)
    {
    }
    public function refund(Invoice $invoice, $eid)
    {
    }
}

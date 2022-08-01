<?php

namespace IdeaToCode\LaravelNovaTallPayments\Payments;

use Illuminate\Http\Request;
use Srmklive\PayPal\Facades\PayPal;
use IdeaToCode\LaravelNovaTallPayments\Models\Log;

use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Facades\Larapay;


class PaypalGateway implements PaymentGatewayInterface
{
    private static $client = null;
    public function getClient()
    {
        if (!is_null(self::$client)) {
            return self::$client;
        }

        $config = config("paypal");

        self::$client = PayPal::setProvider();
        self::$client->setApiCredentials($config);
        $tok = self::$client->getAccessToken();
        self::$client->setAccessToken($tok);

        return self::$client;
    }
    public function createCustomer(Invoice $invoice, $payload)
    {
    }
    public function createSingleCharge(Invoice $invoice, $payload)
    {

        $total = $invoice->amount / 100;
        $provider = $this->getClient();

        $result = $provider->createOrder($data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => config('nova.currency'),
                        "value" => $total
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('payment.success', ['gateway' => 'paypal', 'invoice' => $invoice]),
                "cancel_url" => route('payment.cancel', ['gateway' => 'paypal', 'invoice' => $invoice]),
            ],
        ]);

        $approve_link = null;
        foreach ($result['links'] as $link) {
            if ($link['rel'] == 'approve') {
                $approve_link = $link['href'];
            }
        }

        if ($result['status'] == "CREATED" && !is_null($approve_link)) {
            Log::add($invoice->owner, 'PaypalGateway::createSingleCharge::redirect', [
                'data' => $data,
                'result' => $result,
            ]);
            return (object)[
                'status' => 'redirect',
                'redirect' => $approve_link
            ];
        }

        Log::add($invoice->owner, 'PaypalGateway::createSingleCharge::failed', [
            'data' => $data,
            'result' => $result,
        ]);
        return (object)[
            'status' => 'failed',
            'message' => json_encode($result),
        ];
    }
    public function charge(Invoice $invoice, $payload)
    {
        $token = $payload['request']->token;

        $provider = $this->getClient();
        $result = $provider->capturePaymentOrder($token);

        Log::add($invoice->owner, 'PaypalGateway::charge::succeeded', [
            'result' => $result,
        ]);
        if (@$result['status'] == "COMPLETED") {
            return (object)[
                'status' => 'succeeded',
                'id' => $result['id'],
            ];
        }

        Log::add($invoice->owner, 'PaypalGateway::charge::error', [
            'result' => $result,
        ]);

        if (@$result["type"] == "error") {
            return (object)[
                'status' => 'failed',
                'message' => $result['message'],
            ];
        }
        return (object)[
            'status' => 'failed',
            'message' => 'N/A',
        ];
    }
    public function refund(Invoice $invoice, $eid)
    {
        Log::add($invoice->owner, 'PaypalGateway::refund::refunded', [
            'user' => auth()->user(),
        ]);
        return (object)[
            'id' => 'N/A',
            'status' => 'refunded'
        ];
    }

    public function getPaymentModalView()
    {
        return [
            'larapay::paypal.single', []
        ];
    }

    public function showForm()
    {
        return 'not-ready';
        // return view('larapay::stripe-test',[
        //     'STRIPE_KEY' => env('STRIPE_KEY')
        // ]);
    }

    // public function success(Request $request)
    // {
    //     $provider = $this->getClient();
    //     $result = $provider->capturePaymentOrder($request->token);

    //     if ( @$result['status'] == "COMPLETED" ) {
    //         return (object)[
    //             'id' => $result['id'],
    //             'status' => 'succeeded',
    //         ];
    //     }
    //     if ( @$result["type"] == "error" ) {
    //         return (object)[
    //             'status' => 'failed',
    //             'message' => $result['message'],
    //         ];
    //     }
    //     return (object)[
    //         'status' => 'failed',
    //         'message' => 'N/A',
    //     ];
    // }
}
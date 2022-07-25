<?php

namespace IdeaToCode\LaravelNovaTallPayments\Payments;

use IdeaToCode\LaravelNovaTallPayments\Facades\Larapay;
use Illuminate\Http\Request;

class BitcoinGateway implements PaymentGatewayInterface
{
    private static $client = null;
    public function getClient()
    {
        // if( !is_null(self::$client) ) {
        //     return self::$client;
        // }

        // Stripe::setAppInfo(Larapay::name(), Larapay::version(), Larapay::url());
        // Stripe::setApiKey(env('STRIPE_SECRET'));

        // self::$client = new StripeClient(env('STRIPE_SECRET'));

        // return self::$client;
    }
    public function createCustomer(string $description, string $email)
    {
        // return $this->getClient()->customers->create([
        //     'email' => $email,
        //     'description' => $description,
        // ]);
    }
    public function createSingleCharge(int $amount, string $currency, string $description, string $payment_method = null, string $customer = null, Request $request = null)
    {

        // $data = [
        //     'amount' => $amount,
        //     'currency' => $currency,
        //     'description' => $description,
        //     'confirmation_method' => 'manual',
        //     'confirm' => true,
        // ];

        // if ( ! is_null($request) ) {
        //     $data['payment_method_data'] = [
        //         'type' => 'card',
        //         'card' => ['token' => $request->get('stripeToken')],
        //     ];
        // }

        // if ( ! is_null($payment_method) ) {
        //     $data['payment_method'] = $payment_method;
        // }

        // if ( ! is_null($customer) ) {
        //     $data['customer'] = $customer;
        // }

        // $intent = $this->getClient()->paymentIntents->create($data);

        // return $intent;
    }


    public function showForm()
    {
        return 'not-ready';
        // return view('larapay::stripe-test',[
        //     'STRIPE_KEY' => env('STRIPE_KEY')
        // ]);
    }
}
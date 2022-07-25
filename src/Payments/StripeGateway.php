<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Payments;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Log;
use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPaymentsayments\Facades\Larapay;

class StripeGateway implements PaymentGatewayInterface
{

    public static $cards = [
        'visa' => <<<EOD
                <?xml version="1.0" ?>
<svg id="Layer_1" style="enable-background:new 0 0 156 128;" version="1.1" viewBox="0 0 156 128" xml:space="preserve"
    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <style type="text/css">
    .st0 {
        clip-path: url(#SVGID_2_);
        fill: #FFFFFF;
        stroke: #CBCBCB;
        stroke-width: 2;
        stroke-miterlimit: 10;
    }

    .st1 {
        clip-path: url(#SVGID_4_);
        fill: #F7B600;
    }

    .st2 {
        clip-path: url(#SVGID_6_);
        fill: #1A1F71;
    }

    .st3 {
        clip-path: url(#SVGID_8_);
        fill: #1A1F71;
    }
    </style>
    <g>
        <defs>
            <path d="M14,30c0-3.3,2.7-6,6-6h116c3.3,0,6,2.7,6,6v68c0,3.3-2.7,6-6,6H20c-3.3,0-6-2.7-6-6V30z"
                id="SVGID_1_" />
        </defs>
        <use style="overflow:visible;fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;" xlink:href="#SVGID_1_" />
        <clipPath id="SVGID_2_">
            <use style="overflow:visible;" xlink:href="#SVGID_1_" />
        </clipPath>
        <path class="st0" d="M14,30c0-3.3,2.7-6,6-6h116c3.3,0,6,2.7,6,6v68c0,3.3-2.7,6-6,6H20c-3.3,0-6-2.7-6-6V30z" />
    </g>
    <g>
        <defs>
            <rect height="10.7" id="SVGID_3_" width="117.3" x="19.3" y="88" />
        </defs>
        <clipPath id="SVGID_4_">
            <use style="overflow:visible;" xlink:href="#SVGID_3_" />
        </clipPath>
        <rect class="st1" height="20.7" width="127.3" x="14.3" y="83" />
    </g>
    <g>
        <defs>
            <rect height="10.7" id="SVGID_5_" width="117.3" x="19.3" y="29.3" />
        </defs>
        <clipPath id="SVGID_6_">
            <use style="overflow:visible;" xlink:href="#SVGID_5_" />
        </clipPath>
        <rect class="st2" height="20.7" width="127.3" x="14.3" y="24.3" />
    </g>
    <g>
        <defs>
            <path
                d="M78.2,49.9l-6.1,28.7h-7.4l6.1-28.7H78.2z M109.6,68.4l3.9-10.8l2.3,10.8H109.6z M117.9,78.6h6.9l-6-28.7    h-6.4c-1.4,0-2.7,0.8-3.2,2.1L98,78.6h7.8l1.5-4.3h9.6L117.9,78.6z M98.5,69.2c0-7.6-10.5-8-10.4-11.4c0-1.1,1-2.1,3.2-2.4    c2.5-0.2,5.1,0.2,7.4,1.3l1.3-6.1c-2.2-0.9-4.6-1.3-7-1.3c-7.4,0-12.5,3.9-12.6,9.5c-0.1,4.2,3.7,6.4,6.5,7.8    c2.8,1.4,3.9,2.4,3.9,3.6c0,1.9-2.4,2.8-4.5,2.8c-2.7,0.1-5.3-0.6-7.7-1.8l-1.4,6.3c2.6,1.1,5.4,1.6,8.3,1.5    C93.3,79.1,98.4,75.2,98.5,69.2z M67.6,49.9L55.6,78.6h-7.9l-5.9-23c-0.4-1.4-0.7-1.9-1.7-2.5c-2.3-1.1-4.8-1.9-7.4-2.4l0.2-0.8    h12.7c1.7,0,3.2,1.3,3.4,3L52,69.4l7.7-19.6H67.6z"
                id="SVGID_7_" />
        </defs>
        <clipPath id="SVGID_8_">
            <use style="overflow:visible;" xlink:href="#SVGID_7_" />
        </clipPath>
        <rect class="st3" height="39.7" width="102.1" x="27.7" y="44.3" />
    </g>
</svg>
EOD,
'mastercard' => <<<EOD <?xml version="1.0" ?>
    <svg id="Layer_1" style="enable-background:new 0 0 156 128;" version="1.1" viewBox="0 0 156 128"
        xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <style type="text/css">
        .st0 {
            clip-path: url(#SVGID_2_);
            fill: #FFFFFF;
            stroke: #CBCBCB;
            stroke-width: 2;
            stroke-miterlimit: 10;
        }

        .st1 {
            clip-path: url(#SVGID_4_);
            fill: #F7B600;
        }

        .st2 {
            clip-path: url(#SVGID_6_);
            fill: #1A1F71;
        }

        .st3 {
            clip-path: url(#SVGID_8_);
            fill: #1A1F71;
        }
        </style>
        <g>
            <defs>
                <path d="M14,30c0-3.3,2.7-6,6-6h116c3.3,0,6,2.7,6,6v68c0,3.3-2.7,6-6,6H20c-3.3,0-6-2.7-6-6V30z"
                    id="SVGID_1_" />
            </defs>
            <use style="overflow:visible;fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;" xlink:href="#SVGID_1_" />
            <clipPath id="SVGID_2_">
                <use style="overflow:visible;" xlink:href="#SVGID_1_" />
            </clipPath>
            <path class="st0"
                d="M14,30c0-3.3,2.7-6,6-6h116c3.3,0,6,2.7,6,6v68c0,3.3-2.7,6-6,6H20c-3.3,0-6-2.7-6-6V30z" />
        </g>
        <g>
            <defs>
                <rect height="10.7" id="SVGID_3_" width="117.3" x="19.3" y="88" />
            </defs>
            <clipPath id="SVGID_4_">
                <use style="overflow:visible;" xlink:href="#SVGID_3_" />
            </clipPath>
            <rect class="st1" height="20.7" width="127.3" x="14.3" y="83" />
        </g>
        <g>
            <defs>
                <rect height="10.7" id="SVGID_5_" width="117.3" x="19.3" y="29.3" />
            </defs>
            <clipPath id="SVGID_6_">
                <use style="overflow:visible;" xlink:href="#SVGID_5_" />
            </clipPath>
            <rect class="st2" height="20.7" width="127.3" x="14.3" y="24.3" />
        </g>
        <g>
            <defs>
                <path
                    d="M78.2,49.9l-6.1,28.7h-7.4l6.1-28.7H78.2z M109.6,68.4l3.9-10.8l2.3,10.8H109.6z M117.9,78.6h6.9l-6-28.7    h-6.4c-1.4,0-2.7,0.8-3.2,2.1L98,78.6h7.8l1.5-4.3h9.6L117.9,78.6z M98.5,69.2c0-7.6-10.5-8-10.4-11.4c0-1.1,1-2.1,3.2-2.4    c2.5-0.2,5.1,0.2,7.4,1.3l1.3-6.1c-2.2-0.9-4.6-1.3-7-1.3c-7.4,0-12.5,3.9-12.6,9.5c-0.1,4.2,3.7,6.4,6.5,7.8    c2.8,1.4,3.9,2.4,3.9,3.6c0,1.9-2.4,2.8-4.5,2.8c-2.7,0.1-5.3-0.6-7.7-1.8l-1.4,6.3c2.6,1.1,5.4,1.6,8.3,1.5    C93.3,79.1,98.4,75.2,98.5,69.2z M67.6,49.9L55.6,78.6h-7.9l-5.9-23c-0.4-1.4-0.7-1.9-1.7-2.5c-2.3-1.1-4.8-1.9-7.4-2.4l0.2-0.8    h12.7c1.7,0,3.2,1.3,3.4,3L52,69.4l7.7-19.6H67.6z"
                    id="SVGID_7_" />
            </defs>
            <clipPath id="SVGID_8_">
                <use style="overflow:visible;" xlink:href="#SVGID_7_" />
            </clipPath>
            <rect class="st3" height="39.7" width="102.1" x="27.7" y="44.3" />
        </g>
    </svg>
    EOD,
    'generic' => <<<EOD <?xml version="1.0" ?>
        <!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'>
        <svg id="Layer_1" style="enable-background:new 0 0 64 64;" version="1.1" viewBox="0 0 64 64"
            xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <style type="text/css">
            .st0 {
                fill: #134563;
            }
            </style>
            <g>
                <g id="Icon-Credit-Card" transform="translate(128.000000, 532.000000)">
                    <path class="st0"
                        d="M-76.1-481.5h-39.8c-2.4,0-4.3-1.9-4.3-4.3v-28.5c0-2.4,1.9-4.3,4.3-4.3h39.8     c2.4,0,4.3,1.9,4.3,4.3v28.5C-71.8-483.4-73.7-481.5-76.1-481.5L-76.1-481.5z M-115.9-515.7c-0.8,0-1.4,0.6-1.4,1.4v28.5     c0,0.8,0.6,1.4,1.4,1.4h39.8c0.8,0,1.4-0.6,1.4-1.4v-28.5c0-0.8-0.6-1.4-1.4-1.4H-115.9L-115.9-515.7z"
                        id="Fill-7_1_" />
                    <polygon class="st0" id="Fill-8_1_"
                        points="-118.8,-512.8 -73.2,-512.8 -73.2,-504.3 -118.8,-504.3    " />
                    <polygon class="st0" id="Fill-9_1_"
                        points="-114.5,-498.6 -113.1,-498.6 -113.1,-495.7 -114.5,-495.7    " />
                    <polygon class="st0" id="Fill-10_1_"
                        points="-111.7,-498.6 -110.2,-498.6 -110.2,-495.7 -111.7,-495.7    " />
                    <polygon class="st0" id="Fill-11_1_"
                        points="-108.8,-498.6 -107.4,-498.6 -107.4,-495.7 -108.8,-495.7    " />
                    <polygon class="st0" id="Fill-12_1_"
                        points="-104.5,-498.6 -103.1,-498.6 -103.1,-495.7 -104.5,-495.7    " />
                    <polygon class="st0" id="Fill-13_1_"
                        points="-101.7,-498.6 -100.3,-498.6 -100.3,-495.7 -101.7,-495.7    " />
                    <polygon class="st0" id="Fill-14_1_"
                        points="-98.8,-498.6 -97.4,-498.6 -97.4,-495.7 -98.8,-495.7    " />
                    <polygon class="st0" id="Fill-15_1_"
                        points="-94.6,-498.6 -93.2,-498.6 -93.2,-495.7 -94.6,-495.7    " />
                    <polygon class="st0" id="Fill-16_1_"
                        points="-91.7,-498.6 -90.3,-498.6 -90.3,-495.7 -91.7,-495.7    " />
                    <polygon class="st0" id="Fill-17_1_"
                        points="-88.9,-498.6 -87.5,-498.6 -87.5,-495.7 -88.9,-495.7    " />
                    <polygon class="st0" id="Fill-18_1_"
                        points="-84.6,-498.6 -83.2,-498.6 -83.2,-495.7 -84.6,-495.7    " />
                    <polygon class="st0" id="Fill-19_1_"
                        points="-81.8,-498.6 -80.3,-498.6 -80.3,-495.7 -81.8,-495.7    " />
                    <polygon class="st0" id="Fill-20_1_"
                        points="-78.9,-498.6 -77.5,-498.6 -77.5,-495.7 -78.9,-495.7    " />
                </g>
            </g>
        </svg>
        EOD,
        ];
        private static $client = null;
        public function getClient()
        {
        if (!is_null(self::$client)) {
        return self::$client;
        }

        Stripe::setAppInfo(Larapay::name(), Larapay::version(), Larapay::url());
        Stripe::setApiKey(env('STRIPE_SECRET'));

        self::$client = new StripeClient(env('STRIPE_SECRET'));

        return self::$client;
        }
        public function createCustomer(Invoice $invoice, $payload)
        {
        // string $description, string $email

        $owner = $invoice->owner;

        $customer_id = $owner->stripe_customer_id;

        if (is_null($customer_id)) {

        try {
        $result = $this->getClient()->customers->create([
        'email' => @$owner->email,
        'name' => $owner->name,
        ]);

        $customer_id = $owner->stripe_customer_id = $result->id;
        $owner->save();
        Log::add($owner, 'StripeGateway::createCustomer::setPayload', $result);

        // must save the client

        } catch (\Exception $e) {
        Log::add($owner, 'StripeGateway::createCustomer::failed', $e->getMessage());
        return (object)[
        'status' => 'failed',
        'message' => $e->getMessage(),
        ];
        }
        }
        return (object)[
        'status' => 'setPayload',
        'payload' => $customer_id,
        ];
        }

        public function createSingleCharge(Invoice $invoice, $payload)
        {

        $owner = $invoice->owner;

        $token = $payload;
        $description = $invoice->name;

        $payment_method = $owner->stripe_pm_id;

        $customer_id = $owner->stripe_customer_id;

        $amount = $invoice->amount;

        $data = [
        'amount' => $amount,
        'currency' => config('larapay.currency_code'),
        'description' => $description,
        'confirmation_method' => 'automatic',
        'confirm' => true,
        ];

        if (!is_null($token)) {
        $data['payment_method_data'] = [
        'type' => 'card',
        'card' => ['token' => $token],
        ];
        }

        if (!is_null($payment_method)) {
        $data['payment_method'] = $payment_method;
        } else {
        $data['setup_future_usage'] = 'off_session';
        }

        if (!is_null($customer_id)) {
        $data['customer'] = $customer_id;
        }

        $intent = $invoice->getMetaValue('stripe_payment_intent');

        if (is_null($intent)) {
        $intent = $this->getClient()->paymentIntents->create($data);
        $invoice->addOrUpdateMeta('stripe_payment_intent', $intent->id);
        } else {
        $intent = $this->getClient()->paymentIntents->retrieve($intent);
        }

        if ($intent->status == "requires_action") {
        return (object)[
        'status' => 'setPayload',
        'payload' => [
        'action' => $intent->next_action->type,
        'client_secret' => $intent->client_secret,
        ],
        ];
        }

        $charge = $intent->charges->first();


        if (is_null($payment_method)) {
        $this->saveMethod($owner, $charge);
        }

        if ($intent->status == "succeeded") {
        Log::add($owner, 'StripeGateway::createSingleCharge::succeeded', $intent);
        return (object)[
        'status' => 'succeeded',
        'id' => $charge->id,
        ];
        }

        Log::add($owner, 'StripeGateway::createSingleCharge::failed', $intent);
        return (object)[
        'status' => 'failed',
        'message' => "N/A",
        ];

        return $intent;
        }

        public function saveMethod($owner, $charge)
        {
        $pm_id = $charge->payment_method;
        $card = $charge->payment_method_details->card;
        $pm_card = [
        'last4' => $card->last4,
        'brand' => $card->brand,
        ];

        $owner->stripe_card_data = $pm_card;
        $owner->stripe_pm_id = $pm_id;
        $owner->save();
        }


        public function charge(Invoice $invoice, $payload)
        {
        $intent = $invoice->getMetaValue('stripe_payment_intent');
        $intent = $this->getClient()->paymentIntents->retrieve($intent);

        if ($intent->status == "succeeded") {
        $charge = $intent->charges->first();

        $payment_method = $invoice->owner->stripe_pm_id;

        if (is_null($payment_method)) {
        $this->saveMethod($invoice->owner, $charge);
        }

        return (object)[
        'status' => 'succeeded',
        'id' => $charge->id,
        ];
        }
        return (object)[
        'status' => 'failed',
        'message' => "N/A",
        ];
        }

        public function updatePaymentMethods($param, Invoice $invoice, $payload)
        {
        $owner = $invoice->owner;
        $this->getClient()->paymentMethods->detach($owner->stripe_pm_id, []);
        $owner->stripe_card_data = null;
        $owner->stripe_pm_id = null;
        $owner->save();

        // delete the current payment intent
        $invoice->deleteMeta('stripe_payment_intent');

        return (object)[
        'status' => 'emit',
        'event' => 'openGateway',
        'arguments' => [
        'stripe',
        ]
        ];
        }

        public function refund(Invoice $invoice, $eid)
        {
        Log::add($invoice->owner, 'StripeGateway::refund::refunded', [
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
        'larapay::stripe-single', [
        'STRIPE_KEY' => env('STRIPE_KEY')
        ]
        ];
        }
        public static function getCard($brand)
        {
        if (isset(self::$cards[$brand])) {
        return self::$cards[$brand];
        }
        return self::$cards['generic'];
        }
        }
<?php

namespace AlexEftimie\LaravelPayments;

use App\Models\User;
use NumberFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use AlexEftimie\LaravelPayments\Billable;
use AlexEftimie\LaravelPayments\Contracts\InvoiceManager;
use AlexEftimie\LaravelPayments\Models\Price;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Models\Subscription;
use AlexEftimie\LaravelPayments\Errors\InvalidGateway;
use AlexEftimie\LaravelPayments\Errors\InvoiceAlreadyPaid;
use AlexEftimie\LaravelPayments\Facades\Larapay as LarapayFacade;

class Larapay
{

    protected static $formatter = null;
    protected $name = "LaravelPayments";
    protected $version = "0.0.1";
    protected $url = "https://github.com/alex-eftimie/laravelpayments";

    protected $gateways = [];

    protected $invoice_manager = null;

    protected $product_features = [];

    public function __construct(InvoiceManager $manager = null)
    {
        $this->invoice_manager = $manager;
        $this->gateways = [
            'admin' => [
                'name' => 'Admin',
                'class' => 'AlexEftimie\\LaravelPayments\\Payments\\ManualGateway',
                'description' => 'Pay Manually by Admin',
                'src' => '<svg class="h-12" role="img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" version="1.1" width="48" height="48" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve" class=""><g><g xmlns="http://www.w3.org/2000/svg"><path d="m305.409 253.32c69.84 0 126.65-56.82 126.65-126.66s-56.81-126.66-126.65-126.66-126.66 56.82-126.66 126.66 56.82 126.66 126.66 126.66zm-15-201.11v-3.44c0-8.29 6.71-15 15-15 8.28 0 15 6.71 15 15v3.46c17.85 3.83 31.28 19.73 31.28 38.71 0 8.28-6.72 15-15 15-8.29 0-15-6.72-15-15 0-5.29-4.31-9.6-9.6-9.6h-13.45c-5.29 0-9.6 4.31-9.6 9.6 0 3.03 1.45 5.91 3.89 7.71l12.48 9.24 30.32 22.44c9.96 7.37 15.96 19.16 16.04 31.56v.14c.07 10.58-3.98 20.56-11.42 28.09-5.52 5.59-12.42 9.34-19.94 10.92v3.51c0 8.29-6.72 15-15 15-8.29 0-15-6.71-15-15v-3.46c-7.32-1.56-14.05-5.16-19.49-10.54-7.53-7.43-11.71-17.35-11.77-27.93-.05-8.28 6.62-15.04 14.9-15.09h.1c8.24 0 14.95 6.66 15 14.91.02 5.177 4.185 9.54 9.65 9.54 8.288-.05 5.201-.032 13.43-.08 5.324-.042 9.581-4.363 9.54-9.67v-.14c-.02-3-1.47-5.86-3.89-7.65l-12.47-9.23-30.33-22.44c-10.04-7.44-16.04-19.34-16.04-31.83 0-19.02 13.47-34.93 31.37-38.73z" fill="#008d31" data-original="#000000" style=""/><path d="m100.188 325.392c-3.845-6.666-12.386-8.985-19.093-5.116l-70.558 40.73c-6.681 3.867-8.973 12.412-5.116 19.102l72.114 124.902c3.86 6.679 12.4 8.973 19.093 5.116l70.558-40.74c6.691-3.858 8.974-12.403 5.116-19.093z" fill="#008d31" data-original="#000000" style=""/><path d="m504.478 300.46c-6.79-9.43-19.93-11.56-29.35-4.77-28.58 20.59-83.46 60.13-87.82 63.28-2.01 1.71-4.12 3.26-6.32 4.63-8.63 5.43-18.64 8.33-29.09 8.33h-71.85c-8.28 0-15-6.71-15-15 0-8.3 6.73-15 15-15h76.84c11.29 0 20.33-9.4 19.86-20.71-.44-10.73-9.6-19.05-20.34-19.05h-58.49c-3.96-4.19-8.3-8.03-12.95-11.46-15.99-11.79-35.75-18.76-57.14-18.76-38.03 0-75.87 23.96-91.56 55.94l61.36 106.27h115.1c22.6 0 44.86-5.78 64.45-17.05 6.79-3.9 14.06-8.59 21.94-14.25 33.02-23.72 100.53-73 100.58-73.03 9.44-6.78 11.58-19.94 4.78-29.37z" fill="#008d31" data-original="#000000" style=""/></g></g></svg>',
                'filter' => function () {
                    return Gate::check('pay-manual', new Invoice());
                },
            ],
            'stripe' => [
                'name' => 'Stripe',
                'class' => 'AlexEftimie\\LaravelPayments\\Payments\\StripeGateway',
                'description' => 'Pay using your credit-card using Stripe',
                'src' => '<svg class="h-12" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Stripe</title><path fill="#008CDD" d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.594-7.305h.003z"/></svg>',
                'filter' => function () {
                    return !in_array('stripe', config('larapay.disable_payment_methods'));
                },
            ],
            'paypal' => [
                'name' => 'Paypal',
                'class' => 'AlexEftimie\\LaravelPayments\\Payments\\PaypalGateway',
                'description' => 'Pay using PayPal, using your credit-card or existing PayPal balance',
                'src' => '<svg class="h-12" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>PayPal</title><path fill="#00457C" d="M6.908 24H3.804c-.664 0-1.086-.529-.936-1.18l.149-.674h2.071c.666 0 1.336-.533 1.482-1.182l1.064-4.592c.15-.648.816-1.18 1.48-1.18h.883c3.789 0 6.734-.779 8.84-2.34s3.16-3.6 3.16-6.135c0-1.125-.195-2.055-.588-2.789 0-.016-.016-.031-.016-.046l.135.075c.75.465 1.32 1.064 1.711 1.814.404.75.598 1.68.598 2.791 0 2.535-1.049 4.574-3.164 6.135-2.1 1.545-5.055 2.324-8.834 2.324h-.9c-.66 0-1.334.525-1.484 1.186L8.39 22.812c-.149.645-.81 1.17-1.47 1.17L6.908 24zm-2.677-2.695H1.126c-.663 0-1.084-.529-.936-1.18L4.563 1.182C4.714.529 5.378 0 6.044 0h6.465c1.395 0 2.609.098 3.648.289 1.035.189 1.92.519 2.684.99.736.465 1.322 1.072 1.697 1.818.389.748.584 1.68.584 2.797 0 2.535-1.051 4.574-3.164 6.119-2.1 1.561-5.056 2.326-8.836 2.326h-.883c-.66 0-1.328.524-1.478 1.169L5.7 20.097c-.149.646-.817 1.172-1.485 1.172l.016.036zm7.446-17.369h-1.014c-.666 0-1.332.529-1.48 1.178l-.93 4.02c-.15.648.27 1.179.93 1.179h.766c1.664 0 2.97-.343 3.9-1.021.929-.686 1.395-1.654 1.395-2.912 0-.83-.301-1.445-.9-1.84-.6-.404-1.5-.605-2.686-.605l.019.001z"/></svg>',
                'filter' => function () {
                    return !in_array('paypal', config('larapay.disable_payment_methods'));
                },
            ],
            'bitcoin' => [
                'name' => 'Bitcoin',
                'class' => 'AlexEftimie\\LaravelPayments\\Payments\\BitcoinGateway',
                'description' => 'Pay securely and decentralized using your own Bitcoin',
                'src' => '<svg class="h-12" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Bitcoin</title><path fill="#F7931A" d="M23.638 14.904c-1.602 6.43-8.113 10.34-14.542 8.736C2.67 22.05-1.244 15.525.362 9.105 1.962 2.67 8.475-1.243 14.9.358c6.43 1.605 10.342 8.115 8.738 14.548v-.002zm-6.35-4.613c.24-1.59-.974-2.45-2.64-3.03l.54-2.153-1.315-.33-.525 2.107c-.345-.087-.705-.167-1.064-.25l.526-2.127-1.32-.33-.54 2.165c-.285-.067-.565-.132-.84-.2l-1.815-.45-.35 1.407s.975.225.955.236c.535.136.63.486.615.766l-1.477 5.92c-.075.166-.24.406-.614.314.015.02-.96-.24-.96-.24l-.66 1.51 1.71.426.93.242-.54 2.19 1.32.327.54-2.17c.36.1.705.19 1.05.273l-.51 2.154 1.32.33.545-2.19c2.24.427 3.93.257 4.64-1.774.57-1.637-.03-2.58-1.217-3.196.854-.193 1.5-.76 1.68-1.93h.01zm-3.01 4.22c-.404 1.64-3.157.75-4.05.53l.72-2.9c.896.23 3.757.67 3.33 2.37zm.41-4.24c-.37 1.49-2.662.735-3.405.55l.654-2.64c.744.18 3.137.524 2.75 2.084v.006z"/></svg>',
                'filter' => function () {
                    return !in_array('bitcoin', config('larapay.disable_payment_methods'));
                },
            ],
            'coinbase' => [
                'name' => 'Coinbase',
                'class' => 'AlexEftimie\\LaravelPayments\\Payments\\CoinbaseGateway',
                'description' => 'Pay securely and decentralized with your own Crypto through Coinbase Ecommerce',
                'src' => '<svg class="h-12" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Bitcoin</title><path fill="#F7931A" d="M23.638 14.904c-1.602 6.43-8.113 10.34-14.542 8.736C2.67 22.05-1.244 15.525.362 9.105 1.962 2.67 8.475-1.243 14.9.358c6.43 1.605 10.342 8.115 8.738 14.548v-.002zm-6.35-4.613c.24-1.59-.974-2.45-2.64-3.03l.54-2.153-1.315-.33-.525 2.107c-.345-.087-.705-.167-1.064-.25l.526-2.127-1.32-.33-.54 2.165c-.285-.067-.565-.132-.84-.2l-1.815-.45-.35 1.407s.975.225.955.236c.535.136.63.486.615.766l-1.477 5.92c-.075.166-.24.406-.614.314.015.02-.96-.24-.96-.24l-.66 1.51 1.71.426.93.242-.54 2.19 1.32.327.54-2.17c.36.1.705.19 1.05.273l-.51 2.154 1.32.33.545-2.19c2.24.427 3.93.257 4.64-1.774.57-1.637-.03-2.58-1.217-3.196.854-.193 1.5-.76 1.68-1.93h.01zm-3.01 4.22c-.404 1.64-3.157.75-4.05.53l.72-2.9c.896.23 3.757.67 3.33 2.37zm.41-4.24c-.37 1.49-2.662.735-3.405.55l.654-2.64c.744.18 3.137.524 2.75 2.084v.006z"/></svg>',
                'filter' => function () {
                    return !in_array('coinbase', config('larapay.disable_payment_methods'));
                },
            ]
        ];
    }
    public function gateways()
    {
        return array_filter($this->gateways, function ($g) {
            if (!isset($g['filter'])) {
                return true;
            }
            $func = $g['filter'];

            return $func();
        });
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array($name, $arguments);
        }
        return $this->$name;
    }

    public function driver($gateway)
    {
        $g = $this->gateways;
        if (!isset($g[$gateway])) {
            throw new InvalidGateway;
        }

        return app($g[$gateway]['class']);
    }

    public function formatPrice($amount)
    {

        if (is_null(self::$formatter)) {

            $locale = config('larapay.currency_locale');

            self::$formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        }
        return self::$formatter->formatCurrency($amount / 100, config('larapay.currency_code'));
    }

    public function formatPeriod(Subscription $sub)
    {
        return Price::$period_map[1][$sub->price->billing_period];
    }

    public function getPaymentModalView($gateway)
    {
        $gw = LarapayFacade::driver($gateway);
        return $gw->getPaymentModalView();
    }

    public function dashboard()
    {

        $team = auth()->user()->currentTeam;
        $subs = collect();
        if ($team) {
            $subs = $team->subscriptions()->orderBy('id', 'desc')->with(['price.product', 'latestInvoice'])->get();
        }
        $data = [
            'subscriptions' => $subs,
        ];
        return view('larapay::dashboard', $data);
    }

    public function processResult($result, $gateway, $invoice)
    {
        // TODO: Check correct amount
        if ($result->status == "succeeded") {

            // TODO: All payment gateways should have amount, status and id
            $invoice->pay($gateway, $result->id);

            return redirect()->route('invoice.show', ['invoice' => $invoice]);
        } else if ($result->status == "refunded") {

            // TODO: All payment gateways should have amount, status and id
            $invoice->refund($gateway, $result->id);

            return redirect()->route('invoice.show', ['invoice' => $invoice]);
        } else if ($result->status == "redirect") {
            return redirect()->to($result->redirect);
        } else if ($result->status == "failed") {
            $messageBag = new \Illuminate\Support\MessageBag;
            $messageBag->add('message', htmlspecialchars($result->message));
            $messageBag->add('message', sprintf('<a href="%s" class="font-bold text-gray-400">%s</a>', route('invoice.show', ['invoice' => $invoice]), __('Go Back')));

            return view('larapay::general-error', ['errors' => $messageBag]);
        } else if ($result->status == 'setPayload' || $result->status == 'emit') {
            return $result;
        }

        $messageBag = new \Illuminate\Support\MessageBag;
        $messageBag->add('message', 'Unknown payment status');
        return view('larapay::general-error', ['errors' => $messageBag]);
    }

    public function getManagementRoute($sub)
    {

        if ($sub->isActive()) {
            $manager = $sub->manager;
            $instance = app($manager);
            return $instance->getManagementRoute();
        } else {
            return 'invoice.show';
        }
    }

    public function setAffiliate(User $user)
    {
        $r = request();
        $cv = $r->cookie('AffiliateCookie');

        // Skip if the user does not have an affiliate cookie
        if (is_null($cv)) {
            return;
        }
        $cv = json_decode($cv);

        // Skip if the affiliate code is invalid
        $aff = User::where('affiliate_code', $cv->val)->first();
        if (is_null($aff)) {
            return;
        }
        $user->affiliate_id = $aff->id;
        $user->save();
    }
    public function charge($gateway, Invoice $invoice, $payload)
    {
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->charge($invoice, $payload);
        return $this->processResult($result, $gateway, $invoice);
    }

    public function createSingleCharge($gateway, Invoice $invoice, $payload)
    {
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->createSingleCharge($invoice, $payload);
        return $this->processResult($result, $gateway, $invoice);
    }
    public function cancel($gateway, Invoice $invoice)
    {
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->cancel($invoice);
        return $this->processResult($result, $gateway, $invoice);
    }
    public function callback($gateway, Request $request)
    {
        $gw = LarapayFacade::driver($gateway);
        return $gw->callback($request);
    }

    public function updatePaymentMethods($param, $gateway, Invoice $invoice, $payload)
    {
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->updatePaymentMethods($param, $invoice, $payload);
        return $this->processResult($result, $gateway, $invoice);
    }

    public function createCustomer($gateway, Invoice $invoice, $payload)
    {
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->createCustomer($invoice, $payload);
        return $this->processResult($result, $gateway, $invoice);
    }

    public function refund($gateway, Invoice $invoice)
    {
        $info = $invoice->payment->gateway;
        $gateway = $info->Name;
        $eid = $info->EID;
        $gw = LarapayFacade::driver($gateway);
        $result = $gw->refund($invoice, $eid);
        return $this->processResult($result, $gateway, $invoice);
    }

    /**
     * @see \AlexEftimie\LaravelPayments\Contracts\InvoiceManager
     * @return true if no invoice manager is bound
     * @return true if team billing is set
     * @return false otherwise
     */
    public function isBillable()
    {
        return $this->invoice_manager ? $this->invoice_manager->isBillingSetUp() : true;
    }
    public function downloadInvoiceRoute()
    {
        return optional($this->invoice_manager)->downloadRoute();
    }

    public function productFeatures($features = null)
    {
        if (!is_null($features)) {
            $this->product_features = $features;
        }
        return $this->product_features;
    }
    // public function ShowGatewayForm(Request $request, string $gateway, Invoice $invoice) {

    //     $gw = LarapayFacade::driver($gateway);

    //     $amount = $invoice->amount;

    //     $owner = $invoice->owner;

    //     if ( ! is_null($invoice->paid_at) ) {
    //         throw new InvoiceAlreadyPaid;
    //     }

    //     if ($request->method() == "POST") {

    //         // TODO: Save customer, retrieve existing
    //         $customer = $gw->createCustomer($owner->getName(), $owner->getEmail());

    //         $charge = $gw->createSingleCharge($amount, config('larapay.currency_code'), $invoice->getDescription(), null, $customer->id, $request);

    //         // TODO: Check correct amount
    //         if ($charge->status == "succeeded") {

    //             // TODO: All payment gateways should have amount, status and id
    //             $invoice->pay($gateway, $charge->id);
    //         } else {
    //             // TODO: log this
    //             dd($charge);
    //         }
    //     }
    //     return $gw->showForm();
    // }

    // public function paymentMethodUpdated(Request $request, $gateway, Invoice $invoice, $payload) {

    //     $gw = LarapayFacade::driver($gateway);

    //     $owner = $invoice->owner;
    //     $amount = $invoice->amount;

    //     // TODO: Save customer, retrieve existing
    //     $customer = $gw->createCustomer($owner->getName(), $owner->getEmail());

    //     $result = $gw->createSingleCharge($amount, config('larapay.currency_code'), $invoice->getDescription(), null, $customer->id, $payload);


    //     return $this->processResult($result, $gateway, $invoice);
    // }

}

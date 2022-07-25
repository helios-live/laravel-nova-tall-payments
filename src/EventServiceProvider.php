<?php

namespace IdeaToCode\LaravelNovaTallPayments;

use App\Listeners\EventLog;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Listeners\NotifyUser;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceCreated;
use IdeaToCode\LaravelNovaTallPayments\Listeners\UpdateSales;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceRefunded;
use IdeaToCode\LaravelNovaTallPayments\Listeners\CreatePayment;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionCreated;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionStarted;
use IdeaToCode\LaravelNovaTallPayments\Listeners\CreateCommission;
use IdeaToCode\LaravelNovaTallPayments\Listeners\SetTeamAffiliate;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaymentFailed;
use IdeaToCode\LaravelNovaTallPayments\Events\PayingInvoice;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionNewInvoice;
use IdeaToCode\LaravelNovaTallPayments\Listeners\NotifyInvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Listeners\ExtendSubscription;
use IdeaToCode\LaravelNovaTallPayments\Listeners\NotifyInvoiceCreated;
use IdeaToCode\LaravelNovaTallPayments\Listeners\SubscriptionCreateInvoice;
use IdeaToCode\LaravelNovaTallPayments\Listeners\NotifyInvoicePaymentFailed;
use IdeaToCode\LaravelNovaTallPayments\Listeners\SendInvoicePaid;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        SubscriptionCreated::class => [
            SubscriptionCreateInvoice::class,
        ],
        SubscriptionStarted::class => [],
        SubscriptionInitFailed::class => [],
        SubscriptionNewInvoice::class => [
            SubscriptionCreateInvoice::class,
        ],
        InvoiceCreated::class => [
            NotifyInvoiceCreated::class,
        ],
        PayingInvoice::class => [

            // also sends the subscription started event
            ExtendSubscription::class,
            CreateCommission::class,
            UpdateSales::class,
            SendInvoicePaid::class,
            NotifyInvoicePaid::class,
        ],
        InvoicePaid::class => [],
        InvoicePaymentFailed::class => [
            NotifyInvoicePaymentFailed::class,
        ],
        InvoiceRefunded::class => [
            UpdateSales::class,
        ],
        TeamCreated::class => [
            SetTeamAffiliate::class,
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Invoice::creating(function ($invoice) {
            $invoice->uuid = (string) Str::orderedUuid();
        });
    }

    public function register()
    {
        parent::register();
    }
}
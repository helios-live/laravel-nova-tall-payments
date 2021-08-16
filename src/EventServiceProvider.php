<?php 
namespace AlexEftimie\LaravelPayments;

use App\Listeners\EventLog;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use AlexEftimie\LaravelPayments\Listeners\NotifyUser;
use AlexEftimie\LaravelPayments\Events\InvoiceCreated;
use AlexEftimie\LaravelPayments\Listeners\UpdateSales;
use AlexEftimie\LaravelPayments\Events\InvoiceRefunded;
use AlexEftimie\LaravelPayments\Listeners\CreatePayment;
use AlexEftimie\LaravelPayments\Events\SubscriptionCreated;
use AlexEftimie\LaravelPayments\Events\SubscriptionStarted;
use AlexEftimie\LaravelPayments\Listeners\CreateCommission;
use AlexEftimie\LaravelPayments\Listeners\SetTeamAffiliate;
use AlexEftimie\LaravelPayments\Listeners\ExtendSubscription;
use AlexEftimie\LaravelPayments\Listeners\NotifyInvoiceCreated;
use AlexEftimie\LaravelPayments\Listeners\SubscriptionCreateInvoice;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    protected $listen = [
        SubscriptionCreated::class => [
            SubscriptionCreateInvoice::class,
        ],
        SubscriptionStarted::class => [
        ],
        SubscriptionInitFailed::class => [

        ],
        InvoiceCreated::class => [
            NotifyInvoiceCreated::class,
        ],
        InvoicePaid::class => [

            // also sends the subscription started event
            ExtendSubscription::class,
            CreateCommission::class,
            UpdateSales::class,
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
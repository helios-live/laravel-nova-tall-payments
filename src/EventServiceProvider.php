<?php 
namespace AlexEftimie\LaravelPayments;

use AlexEftimie\LaravelPayments\Events\InvoiceCreated;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use AlexEftimie\LaravelPayments\Events\SubscriptionCreated;
use AlexEftimie\LaravelPayments\Listeners\SubscriptionCreateInvoice;
use AlexEftimie\LaravelPayments\Listeners\SubscriptionExtend;

class EventServiceProvider extends ServiceProvider {

    protected $listen = [
        SubscriptionCreated::class => [
            SubscriptionCreateInvoice::class,
        ],
        InvoiceCreated::class => [],
        InvoicePaid::class => [
            SubscriptionExtend::class,
        ],
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
    }

    public function register()
    {
        parent::register();
    }
}
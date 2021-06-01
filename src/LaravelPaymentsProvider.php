<?php

namespace AlexEftimie\LaravelPayments;

use Livewire\Livewire;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use AlexEftimie\LaravelPayments\Larapay;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Models\Payment;
use AlexEftimie\LaravelPayments\Models\Subscription;
use AlexEftimie\LaravelPayments\EventServiceProvider;
use AlexEftimie\LaravelPayments\Policies\InvoicePolicy;
use AlexEftimie\LaravelPayments\Livewire\InvoiceManager;
use AlexEftimie\LaravelPayments\Livewire\TeamBillingManager;
use AlexEftimie\LaravelPayments\Console\Commands\CronSubscriptions;
use AlexEftimie\LaravelPayments\Components\ButtonLink;

// "AlexEftimie\\LaravelPayments\\": "vendor/alexeftimie/laravel-payments/src/"
class LaravelPaymentsProvider extends ServiceProvider
{
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('larapay', function ($app) { return new Larapay(); });
        $this->app->register(EventServiceProvider::class);
        $this->mergeConfigFrom( __DIR__.'/../config/larapay.php', 'larapay'  );


        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Subscription', Subscription::class);
        $loader->alias('Payment', Payment::class);
        $loader->alias('Invoice', Invoice::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        require __DIR__ . '/routes.php';

        // Blade::componentNamespace('AlexEftimie\\LaravelPayments\\Views\\Components', 'larapay');

        Livewire::component('larapay::team-billing-manager', TeamBillingManager::class);
        Livewire::component('larapay::invoice-manager', InvoiceManager::class);

        $this->publishes([ 
            __DIR__.'/../config/larapay.php' => config_path('larapay.php'), 
            __DIR__.'/../config/paypal.php' => config_path('paypal.php'), 
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ( $this->app->env == "testing") {
            $this->loadMigrationsFrom(__DIR__ . '/../tests/database/migrations');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'larapay');

        Blade::component(ButtonLink::class, 'button-link');

        Route::model('invoice', Invoice::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                CronSubscriptions::class,
            ]);
        }
        
        $this->registerPolicies();
    }
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }
}

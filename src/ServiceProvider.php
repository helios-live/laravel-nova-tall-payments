<?php

namespace IdeaToCode\LaravelNovaTallPayments;

use Livewire\Livewire;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use IdeaToCode\LaravelNovaTallPayments\Larapay;
use IdeaToCode\LaravelNovaTallPayments\Models\Log;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Models\Payment;
use IdeaToCode\LaravelNovaTallPayments\Policies\LogPolicy;
use IdeaToCode\LaravelNovaTallPayments\Models\Subscription;
use IdeaToCode\LaravelNovaTallPayments\EventServiceProvider;
use IdeaToCode\LaravelNovaTallPayments\Policies\PricePolicy;
use IdeaToCode\LaravelNovaTallPayments\Components\ButtonLink;
use IdeaToCode\LaravelNovaTallPayments\Policies\InvoicePolicy;
use IdeaToCode\LaravelNovaTallPayments\Livewire\InvoiceManager;
use IdeaToCode\LaravelNovaTallPayments\Livewire\TeamBillingManager;
use IdeaToCode\LaravelNovaTallPayments\Policies\SubscriptionPolicy;
use IdeaToCode\LaravelNovaTallPayments\Console\Commands\CronSubscriptions;
use IdeaToCode\LaravelNovaTallPayments\Nova\Subscription as NovaSubscription;

class ServiceProvider extends IlluminateServiceProvider
{
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
        Log::class => LogPolicy::class,
        Price::class => PricePolicy::class,
        Subscription::class => SubscriptionPolicy::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('larapay', function ($app) {
            return app(Larapay::class);
        });
        $this->app->register(EventServiceProvider::class);
        $this->mergeConfigFrom(__DIR__ . '/../config/larapay.php', 'larapay');


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

        $dr = Config::get('nova.dynamic_resources') ?? [];
        $scanned_directory = array_diff(scandir(__DIR__ . '/Nova/'), array('..', '.'));
        foreach ($scanned_directory as $file) {
            if (substr($file, -4) != ".php") {
                continue;
            }
            $name = substr($file, 0, -4);
            $dr['alex-eftimie/laravel-payments/' . $name] = 'IdeaToCode\\LaravelNovaTallPayments\\Nova\\' . $name;
        }

        Config::set('nova.dynamic_resources', $dr);

        require __DIR__ . '/routes.php';

        // Blade::componentNamespace('IdeaToCode\\LaravelNovaTallPayments\\Views\\Components', 'larapay');

        Livewire::component('larapay::team-billing-manager', TeamBillingManager::class);
        Livewire::component('larapay::invoice-manager', InvoiceManager::class);

        $this->publishes([
            __DIR__ . '/../config/larapay.php' => config_path('larapay.php'),
            __DIR__ . '/../config/paypal.php' => config_path('paypal.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->env == "testing") {
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
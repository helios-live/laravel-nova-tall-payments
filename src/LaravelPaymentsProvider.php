<?php

namespace AlexEftimie\LaravelPayments;

use AlexEftimie\LaravelPayments\Console\Commands\CronSubscriptions;
use Illuminate\Support\ServiceProvider;

class LaravelPaymentsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CronSubscriptions::class,
            ]);
        }
    }
}

<?php

namespace AlexEftimie\LaravelPayments\Tests;


use AlexEftimie\LaravelPayments\LaravelPaymentsProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return AlexEftimie\LaravelPayments\LaravelPaymentsProvider
     */
    protected function getPackageProviders($app)
    {
        return [MyPackageServiceProvider::class];
    }
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'MyPackage' => MyPackageFacade::class,
        ];
    }
}
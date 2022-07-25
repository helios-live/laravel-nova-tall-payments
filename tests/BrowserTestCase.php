<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use IdeaToCode\LaravelNovaTallPayments\LaravelPaymentsProvider;
use IdeaToCode\LaravelNovaTallPayments\Tests\SetupTests;


class BrowserTestCase extends BaseTestCase {
    use SetupTests;

    public function setUp(): void {
        parent::setUp();
        $this->artisan('migrate:fresh', ['--seed' => false]);
    }
    protected function getEnvironmentSetUp($app) {

        $app->useStoragePath(realpath(__DIR__.'/console/laravel.log'));
        $app['config']->set('logging.channels.single', [
            'driver' => 'single',
            'path' => realpath(__DIR__.'/console/laravel.log'),
            'level' => 'debug',
        ]);

    }
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('local', 'local');
        $app['config']->set('debug', true);
        $app['config']->set('cipher', 'AES-256-CBC');
    }
    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate', ['--database' => 'mysql']);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelPaymentsProvider::class,
        ];
    }
}
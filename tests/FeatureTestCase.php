<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use IdeaToCode\LaravelNovaTallPaymentsayments\LaravelPaymentsProvider;
use IdeaToCode\LaravelNovaTallPaymentsayments\Tests\SetupTests;


class FeatureTestCase extends BaseTestCase {
    use SetupTests;
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();
        $this->refreshDatabase();
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
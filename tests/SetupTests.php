<?php

namespace AlexEftimie\LaravelPayments\Tests;

use AlexEftimie\LaravelPayments\Models\Price;
use Orchestra\Testbench\Factories\UserFactory;
use AlexEftimie\LaravelPayments\Tests\Models\User;
use AlexEftimie\LaravelPayments\Models\Subscription;

/**
 * 
 */
trait SetupTests
{
    public $owner;
    public $price;
    public $sub;
    public $amount;
    public $invoice;

    public function SetupSubscription() {

        $this->amount = mt_rand() % 100000;

        // create
        $user = UserFactory::new()->create();

        // load as Tests/User
        $this->owner = User::latest()->first();

        $this->price = (new Price())->fill([
            'name' => 'Test Price',
            'slug' => 'test-price',
            'product_id' => 1,
            // 'is_recurring' => true,
            'amount' => $this->amount,
            'billing_period' => '1d',
        ]);
        $this->price->save();

        $this->sub = Subscription::NewSubscription($this->owner, $this->price, null);

        $this->invoice = $this->sub->invoices()->first();

    }
}

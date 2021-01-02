<?php

namespace AlexEftimie\LaravelPayments\Tests\Feature;

use AlexEftimie\LaravelPayments\Models\Price;
use AlexEftimie\LaravelPayments\Models\Subscription;
use App\Models\Team;

/**
 * 
 */
trait SetupTests
{
    public $team;
    public $price;
    public $sub;
    public $amount;
    public $invoice;

    public function SetupSubscription() {

        $this->amount = mt_rand() % 100000;

        $this->team = (new Team())->fill([
            'user_id' => 1,
            'name' => 'test team',
            'personal_team' => false,
        ]);
        $this->team->save();

        $this->price = (new Price())->fill([
            'name' => 'Test Price',
            'slug' => 'test-price',
            'product_id' => 1,
            // 'is_recurring' => true,
            'amount' => $this->amount,
            'billing_period' => '1d',
        ]);
        $this->price->save();

        $this->sub = Subscription::NewSubscription($this->team, $this->price, null);

        $this->invoice = $this->sub->invoices()->first();

    }
}

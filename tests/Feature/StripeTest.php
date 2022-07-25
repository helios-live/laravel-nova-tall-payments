<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use IdeaToCode\LaravelNovaTallPayments\Models\Price;
use IdeaToCode\LaravelNovaTallPayments\Tests\FeatureTestCase;
use IdeaToCode\LaravelNovaTallPayments\Payments\StripeGateway;


class StripeTest extends FeatureTestCase
{
    use WithFaker;


    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_client_created()
    {
        $gw = new StripeGateway();

        $client = $gw->getClient();

        $this->assertNotNull($client);
    }


    public function test_createCustomer()
    {
        $gw = new StripeGateway();

        $client = $gw->getClient();

        $fname = $this->faker->firstName();
        $lname = $this->faker->lastName();
        $email = $this->faker->email;

        $customer = $gw->createCustomer($fname . ' ' . $lname, $email);

        $this->assertEquals($fname . ' ' . $lname, $customer->description);

        $this->assertEquals($email, $customer->email);
    }

    // public function test_createSingleCharge_existing_customer()
    // {
    //     $gw = new StripeGateway();

    //     $client = $gw->getClient();

    //     $fname = $this->faker->firstName();
    //     $lname = $this->faker->lastName();
    //     $email = $this->faker->email;


    //     $customer = $gw->createCustomer($fname . ' ' . $lname, $email);

    //     $charge = $gw->createSingleCharge(100, 'USD', 'Test Charge', null, $customer->id, null);

    //     $id = $charge->id;

    //     $this->assertNotNull($id);
    // }
}

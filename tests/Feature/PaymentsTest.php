<?php

namespace AlexEftimie\LaravelPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Tests\FeatureTestCase;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use AlexEftimie\LaravelPayments\Events\InvoiceCreated;

class PaymentsTest extends FeatureTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->SetupSubscription();
    }

    public function test_payment_created_when_invoice_paid()
    {
        $now = Carbon::now();
        $this->invoice->pay('Test GW', 'Test ID');

        $payment = $this->invoice->payments()->first();

        $this->assertNotNull($payment);

        $this->assertEquals('Test GW', $payment->gateway['Name']);
        $this->assertEquals('Test ID', $payment->gateway['EID']);
        $this->assertEquals($this->invoice->amount, $payment->amount);

    }
}

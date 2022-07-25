<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Tests\FeatureTestCase;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceCreated;

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

<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use IdeaToCode\LaravelNovaTallPayments\Tests\FeatureTestCase;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceCreated;

class InvoicesTest extends FeatureTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->SetupSubscription();
    }

    public function test_event_is_emitted_when_invoice_created()
    {
        Event::fake();

        $invoice = $this->owner->invoices()->create([
        	'uuid' => Invoice::newUuid(),
            'subscription_id' => $this->sub->id,
            'amount' => $this->amount,
            'due_at' => Carbon::now(),
        ]);

        $this->assertNull($this->invoice->paid_at, "Invoice was already paid");


        Event::assertDispatched(InvoiceCreated::class, function ($event) use ($invoice) {
            return $event->invoice->id === $invoice->id;
        });
    }

    public function test_event_is_emitted_when_invoice_paid()
    {
        Event::fake();
        $now = Carbon::now();
        $this->invoice->pay('Test GW', 'Test ID');

        $this->assertEquals($this->invoice->paid_at->format('Y-m-d H:i:s'), $now, "Invoice was not paid correctly");

        $invoice = $this->invoice;
        Event::assertDispatched(InvoicePaid::class, function ($event) use ($invoice) {
            return $event->invoice->id === $invoice->id;
        });
    }
}

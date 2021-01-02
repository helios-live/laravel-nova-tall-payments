<?php

namespace AlexEftimie\LaravelPayments\Tests\Feature;

use AlexEftimie\LaravelPayments\Events\InvoiceCreated;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InvoicesTest extends TestCase
{
    use RefreshDatabase;

    use SetupTests;

    public function setUp(): void
    {
        parent::setUp();
        $this->SetupSubscription();
    }

    public function test_event_is_emitted_when_invoice_created()
    {
        Event::fake();

        $invoice = $this->sub->invoices()->create([
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
        $this->invoice->pay();

        $this->assertEquals($this->invoice->paid_at->format('Y-m-d H:i:s'), $now, "Invoice was not paid correctly");

        $invoice = $this->invoice;
        Event::assertDispatched(InvoicePaid::class, function ($event) use ($invoice) {
            return $event->invoice->id === $invoice->id;
        });
    }
}

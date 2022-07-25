<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use IdeaToCode\LaravelNovaTallPayments\Tests\FeatureTestCase;
use IdeaToCode\LaravelNovaTallPayments\Models\Subscription;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionEnded;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionCreated;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionCanceled;

class SubscriptionsTest extends FeatureTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->SetupSubscription();
    }

    public function test_subscription_is_created_correctly()
    {
        // Status of new subscriptions should be New
        $this->assertEquals($this->sub->status, 'New');

        // Price should be equal to $this->amount
        $this->assertEquals($this->amount, $this->sub->current_price);

        // Expiration Date should be null
        $this->assertNull($this->sub->expires_at, "Subscription expiration date should be null");
    }
    /**
     * Tests that the SubscriptionCreated is emitted.
     *
     * @return void
     */
    public function test_event_is_emitted_when_subscription_created()
    {
        Event::fake();
        $this->sub = Subscription::NewSubscription($this->owner, $this->price, null);
        $sub = $this->sub;
        // The event should be emitted with the right subscription
        Event::assertDispatched(SubscriptionCreated::class, function ($event) use ($sub) {
            return $event->subscription->id === $sub->id;
        });

    }

    public function test_invoice_is_correct_when_subscription_created()
    {
        $date = Carbon::now()->add('24 hours');
        $invoice = $this->sub->invoices()->first();

        // Is the invoice created?
        $this->assertNotNull($invoice, "The invoice was not created");

        // expiration should be 24 hours later
        $this->assertEquals($date->format('Y-m-d H:i:s'), $invoice->due_at->format('Y-m-d H:i:s'));

        // Amount should be equal to subscription price
        $this->assertEquals($this->sub->current_price, $invoice->amount);
    }

    public function test_subscription_extended_when_invoice_paid()
    {

        $price = $this->sub->price;

        $date = $price->getNextPeriodFrom(Carbon::now());
        $expected = $date->format('Y-m-d H:i:s');

        $this->invoice->pay('Test GW','Test Id');

        $this->sub->refresh();

        $this->assertNotNull($this->sub->expires_at, "Expires_at should not be null after payment");

        $actual = $this->sub->expires_at->format('Y-m-d H:i:s');
        $this->assertEquals($expected, $actual);
    }

    public function test_subscription_status_updated_after_canceled()
    {
        $this->sub->cancel();
        $this->assertEquals('Canceled', $this->sub->status);
    }

    public function test_subscription_status_and_event_emitted_after_canceled()
    {
        Event::fake();
        $this->sub->cancel();

        $this->sub->refresh();

        // test subscription canceled
        $this->assertEquals('Canceled', $this->sub->status);

        $sub = $this->sub;

        // The event should be emitted with the right subscription
        Event::assertDispatched(SubscriptionCanceled::class, function ($event) use ($sub) {
            return $event->subscription->id === $sub->id;
        });

        Event::assertNotDispatched(SubscriptionEnded::class);
    }

    public function test_subscription_ended_after_expiration()
    {

        // get past date
        $date = Carbon::now()->sub('1 month 1 hour');

        // Set time to past
        Carbon::setTestNow($date);

        // Pay invoice in the past
        $this->invoice->pay('test gw', 'test id');

        // Run Cron Command
        $this->artisan('payments:cron')
            ->assertExitCode(0);

        $this->sub->refresh();
        
        // $this->sub->end();
        $this->assertEquals('Ended', $this->sub->status);

        $end_reason = $this->sub->getMetaValue('end_reason');

        // Check end reason
        $this->assertEquals(Subscription::REASON_EXPIRED, $end_reason);
    }

    public function test_subscription_status_updated_after_ended()
    {
        $this->sub->end("Test");
        $this->assertEquals('Ended', $this->sub->status);
    }

    public function test_subscription_event_emitted_after_ended()
    {
        Event::fake();
        $this->sub->end("Test");

        $this->sub->refresh();

        $sub = $this->sub;

        // The event should be emitted with the right subscription
        Event::assertDispatched(SubscriptionEnded::class, function ($event) use ($sub) {
            return $event->subscription->id === $sub->id;
        });

        Event::assertNotDispatched(SubscriptionCanceled::class);
    }
}
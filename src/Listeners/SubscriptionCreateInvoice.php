<?php

namespace IdeaToCode\LaravelNovaTallPayments\Listeners;


use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionEvent;
use IdeaToCode\LaravelNovaTallPayments\Models\Invoice;
use Carbon\Carbon;

class SubscriptionCreateInvoice
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SubscriptionEvent $event)
    {
        $sub = $event->subscription;

        // Completely New Subscription
        if ($sub->status == 'New') {

            // First invoice has a 24 hour deadline
            $date = Carbon::now()->add('24 hours');
        } else {
            $date = $sub->expires_at;
        }

        $owner = $sub->owner;

        $owner->invoices()->create([
            'uuid' => Invoice::newUuid(),
            'subscription_id' => $sub->id,
            'amount' => $sub->current_price,
            'plus' => null,
            'due_at' => $date,
        ]);
    }
}
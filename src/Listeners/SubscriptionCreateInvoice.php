<?php

namespace AlexEftimie\LaravelPayments\Listeners;


use AlexEftimie\LaravelPayments\Events\SubscriptionEvent;
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
        if ( $sub->status == 'New' ) {

            // First invoice has a 24 hour deadline
            $date = Carbon::now()->add('24 hours');
        } else {
            $date = $sub->expires_at;
        }

        $sub->invoices()->create([
            'amount' => $sub->current_price,
            'plus' => null,
            'due_at' => $date,
        ]);

    }
}

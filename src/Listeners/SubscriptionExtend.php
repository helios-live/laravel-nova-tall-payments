<?php

namespace AlexEftimie\LaravelPayments\Listeners;


use AlexEftimie\LaravelPayments\Events\InvoiceEvent;
use Carbon\Carbon;

class SubscriptionExtend
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InvoiceEvent $event)
    {
        $sub = $event->invoice->subscription;

        // Completely New Subscription
        if ( $sub->status == 'New' ) {

            // First invoice has a 24 hour deadline
            $date = Carbon::now();
        } else {
            $date = $sub->expires_at;
        }
        
        $date = $sub->price->getNextPeriodFrom($date);
        
        $sub->expires_at = $date;
        $sub->save();

    }
}

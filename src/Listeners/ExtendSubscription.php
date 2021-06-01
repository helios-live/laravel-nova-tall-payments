<?php

namespace AlexEftimie\LaravelPayments\Listeners;


use Carbon\Carbon;


use AlexEftimie\LaravelPayments\Events\InvoiceEvent;

class ExtendSubscription
{
    /**
     * Extends the expiration date.
     *
     * @param  InvoiceEvent  $event
     * @return void
     */
    public function handle(InvoiceEvent $event)
    {
        $invoice = $event->invoice;

        // invoice is not for a subscription
        if ( $invoice->subscription == null ) {
            return;
        } 
        $sub = $invoice->subscription;

        // Completely New Subscription
        if ( $sub->status == 'New' || $sub->status == 'Waiting' ) {

            // First invoice has a 24 hour deadline
            $date = Carbon::now();
            $sub->start();
        } else {
            $date = $sub->expires_at;
        }
        
        $date = $sub->price->getNextPeriodFrom($date);
        
        $sub->expires_at = $date;
        $sub->save();

    }
}

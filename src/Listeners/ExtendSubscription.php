<?php

namespace AlexEftimie\LaravelPayments\Listeners;


use Carbon\Carbon;


use AlexEftimie\LaravelPayments\Events\InvoiceEvent;
use AlexEftimie\LaravelPayments\Events\SubscriptionExtended;

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
        if ($invoice->subscription == null) {
            return;
        }
        $sub = $invoice->subscription;

        $isNew = $sub->status == 'New' || $sub->status == 'Waiting';
        // Completely New Subscription
        if ($isNew) {

            // First invoice has a 24 hour deadline
            $date = Carbon::now();
            $sub->start();
        } else {
            if ($sub->expires_at->isPast()) {
                $date = Carbon::now();
            } else {
                $date = $sub->expires_at;
            }
        }

        $date = $sub->price->getNextPeriodFrom($date);

        $sub->expires_at = $date;
        $sub->save();

        if (!$isNew) {
            event(new SubscriptionExtended($sub));
        }
    }
}

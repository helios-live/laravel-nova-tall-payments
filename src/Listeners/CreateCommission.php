<?php

namespace AlexEftimie\LaravelPayments\Listeners;


use AlexEftimie\LaravelPayments\Events\InvoiceEvent;
use Carbon\Carbon;

class CreateCommission
{
    /**
     * Create commission if necessary.
     *
     * @param  InvoiceEvent  $event
     * @return void
     */
    public function handle(InvoiceEvent $event)
    {
        $invoice = $event->invoice;

        $sub = $invoice->subscription;

        if ( $sub->affiliate != null ) {

            $commission = $sub->affiliate->commissions()->create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->amount * config('larapay.commission'),
            ]);
        }

    }
}

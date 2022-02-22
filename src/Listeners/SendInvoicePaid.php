<?php

namespace AlexEftimie\LaravelPayments\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use AlexEftimie\LaravelPayments\Events\InvoiceEvent;
use AlexEftimie\LaravelPayments\Notifications\InvoicePaid as InvoicePaidNotification;

class SendInvoicePaid
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InvoiceEvent $event)
    {
        $invoice = $event->invoice;
        $payment = $invoice->payment;
        $event = new InvoicePaid($invoice);
        $event->setGateway($payment->gateway->Name, $payment->gateway->EID);
        event($event);
    }
}
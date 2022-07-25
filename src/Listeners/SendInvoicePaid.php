<?php

namespace IdeaToCode\LaravelNovaTallPayments\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceEvent;
use IdeaToCode\LaravelNovaTallPayments\Notifications\InvoicePaid as InvoicePaidNotification;

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

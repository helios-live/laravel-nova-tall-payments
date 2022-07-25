<?php

namespace IdeaToCode\LaravelNovaTallPayments\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use IdeaToCode\LaravelNovaTallPayments\Notifications\InvoicePaymentFailed as InvoicePaymentFailedNotification;

class NotifyInvoicePaymentFailed
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
    public function handle($event)
    {
        // if the event has been displayed in the browser, do not send an email
        if (isset($event->seen) && $event->seen) {
            return;
        }
        $notification = new InvoicePaymentFailedNotification($event->invoice);
        $team = $event->owner;
        $user = $team->owner;
        $user->notify($notification);
    }
}

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
        $owner = $event->owner;
        if (!is_callable([$owner, 'notify'])) {
            $owner = $owner->owner;
        }
        $owner->notify($notification);
    }
}

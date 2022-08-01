<?php

namespace IdeaToCode\LaravelNovaTallPayments\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use IdeaToCode\LaravelNovaTallPayments\Notifications\InvoicePaid as InvoicePaidNotification;

class NotifyInvoicePaid
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
        $notification = new InvoicePaidNotification($event->invoice);
        $owner = $event->owner;
        if (!is_callable([$owner, 'notify'])) {
            $owner = $owner->owner;
        }
        $owner->notify($notification);
    }
}

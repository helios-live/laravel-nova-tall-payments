<?php

namespace AlexEftimie\LaravelPayments\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use AlexEftimie\LaravelPayments\Notifications\InvoicePaid as InvoicePaidNotification;

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
        $team = $event->owner;
        $user = $team->owner;
        $user->notify($notification);
    }
}

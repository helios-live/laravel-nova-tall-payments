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
        $team = $event->owner;
        $user = $team->owner;
        $user->notify($notification);
    }
}

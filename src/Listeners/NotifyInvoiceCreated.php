<?php

namespace AlexEftimie\LaravelPayments\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use AlexEftimie\LaravelPayments\Events\UserEvent;
use AlexEftimie\LaravelPayments\Events\InvoiceEvent;
use Illuminate\Contracts\Container\BindingResolutionException;
use AlexEftimie\LaravelPayments\Notifications\InvoiceCreated as InvoiceCreatedNotification;

class NotifyInvoiceCreated extends UserEvent
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
        $notification = new InvoiceCreatedNotification($event->invoice);
        $team = $event->owner;
        $user = $team->owner;
        $user->notify($notification);

    }
}

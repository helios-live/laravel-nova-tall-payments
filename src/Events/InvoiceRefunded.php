<?php

namespace IdeaToCode\LaravelNovaTallPayments\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceRefunded extends InvoiceEvent
{
    public $gateway;
    public $id;

    public function setGateway(string $gateway, string $id)
    {
        $this->gateway = $gateway;
        $this->id = $id;
    }
}

<?php

namespace AlexEftimie\LaravelPayments\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use AlexEftimie\LaravelPayments\Models\Sale;
use AlexEftimie\LaravelPayments\Events\InvoiceEvent;

class UpdateSales
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
        $amount = 0;
        switch(class_basename($event)) {
            case "InvoiceRefunded":
                $amount = -1 * $invoice->amount;
                break;
            case "InvoicePaid":
                $amount = $invoice->amount;
                break;
            default:
                return;
        }
        Log::info(class_basename($event), [$invoice->id, $invoice->amount]);
        $sub = $invoice->subscription;
        $price = $sub->price;
        $prod = $price->product;
        $team = $invoice->owner;
        Sale::create([
            'amount' => $amount,
            'team_id' => $team->id,
            'product_id' => $prod->id,
            'price_id' => $price->id,
        ])->save();
    }
}

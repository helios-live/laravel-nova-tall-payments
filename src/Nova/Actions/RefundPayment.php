<?php

namespace IdeaToCode\LaravelNovaTallPayments\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Http\Requests\NovaRequest;
use IdeaToCode\LaravelNovaTallPayments\Facades\Larapay;

class RefundPayment extends DestructiveAction
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        return Action::danger('Invalid invoice status, must be "paid"!');
        foreach ($models as $model) {
            $ret = $this->refund($model);
            if ($ret !== true) {
                return Action::danger($ret);
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    private function refund($invoice)
    {
        if ($invoice->status != 'paid') {
            return 'Invalid invoice status, must be "paid"!';
        }
        $info = $invoice->payment->gateway;
        $gateway = $info->Name;
        $eid = $info->EID;
        $gw = Larapay::driver($gateway);
        $result = $gw->refund($invoice, $eid);
        if ($result->status == 'refunded') {
            $invoice->refund($gateway, $result->id, $invoice->amount);
            return true;
        }
        return "Failed to refund, status: " . $result->status;
    }
}
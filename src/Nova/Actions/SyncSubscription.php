<?php

namespace AlexEftimie\LaravelPayments\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use AlexEftimie\LaravelPayments\Events\SubscriptionSync;

class SyncSubscription extends Action
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



        $model = $models->first();
        $pp = app($model->manager);
        try {
            $res = $pp->syncSubscription($model);
            if (is_string($res)) {
                return Action::message($res);
            }
        } catch (\Exception $e) {
            return Action::danger($e->getMessage());
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}

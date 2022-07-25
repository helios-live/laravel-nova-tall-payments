<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Nova;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Currency;

use App\Nova\Traits\ReferralTrait;
use Laravel\Nova\Fields\MorphMany;
use IdeaToCode\LaravelNovaTallPaymentsayments\Nova\Meta;
use Laravel\Nova\Http\Requests\NovaRequest;
use IdeaToCode\LaravelNovaTallPaymentsayments\Facades\Larapay;
use IdeaToCode\LaravelNovaTallPaymentsayments\Nova\Actions\EndSubscription;
use IdeaToCode\LaravelNovaTallPaymentsayments\Nova\Actions\SyncSubscription;
use IdeaToCode\LaravelNovaTallPaymentsayments\Nova\Actions\CancelSubscription;
use IdeaToCode\LaravelNovaTallPaymentsayments\Nova\Filters\SubscriptionStatus;

class Subscription extends Resource
{
    public static $group = 'Billing';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \IdeaToCode\LaravelNovaTallPaymentsayments\Models\Subscription::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    // public static $title = 'id';

    public function title()
    {
        return '#' . $this->id . '. ' . $this->name;
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('Name', 'name')->asHtml()->readonly(),

            Text::make('Manage', function () {
                $mod = $this->resource;
                $route = Larapay::getManagementRoute($mod);
                $link = route($route, $mod);
                if ($route == 'invoice.show') {
                    $invoice = $mod->latestInvoice;
                    if ($invoice) {
                        $link = route($route, $mod->latestInvoice);
                    } else {
                        $link = route('proxypanel::manage', $mod);
                    }
                }
                return '<a href="' . $link . '" class="no-underline text-xl">⚙️</a>';
            })->asHtml(),

            MorphTo::make('Owner')
                ->hideWhenUpdating(),

            Currency::make('Price', 'current_price')->asMinorUnits(),

            Text::make('Status')->readonly()->sortable(),

            HasMany::make('Invoices', 'invoices', Invoice::class),

            Code::make('Payload')
                ->help("If you change this payload, the next next payment cycle will be affected")
                ->json(),

            MorphMany::make('Meta', 'meta', Meta::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            SubscriptionStatus::make(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new CancelSubscription,
            new EndSubscription,
            (new SyncSubscription)->onlyOnDetail()->canSee(function () {
                if (!isset($this->resource->price)) {
                    return true;
                }
                return !is_null($this->resource->price->product->skumodel);
            })->confirmText('Are you sure you want to sync this subscription, it might change items order?')
                ->confirmButtonText('Sync')
                ->cancelButtonText("Don't sync"),
        ];
    }
}
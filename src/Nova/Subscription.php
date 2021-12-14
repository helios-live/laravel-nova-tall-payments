<?php

namespace AlexEftimie\LaravelPayments\Nova;

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
use AlexEftimie\LaravelPayments\Nova\Meta;
use Laravel\Nova\Http\Requests\NovaRequest;
use AlexEftimie\LaravelPayments\Facades\Larapay;
use AlexEftimie\LaravelPayments\Nova\Actions\EndSubscription;
use AlexEftimie\LaravelPayments\Nova\Actions\SyncSubscription;
use AlexEftimie\LaravelPayments\Nova\Actions\CancelSubscription;

class Subscription extends Resource
{
    public static $group = 'Billing';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \AlexEftimie\LaravelPayments\Models\Subscription::class;

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
                $route = Larapay::getManagementRoute($this);
                $link = route($route, $this);
                if ($route == 'invoice.show') {
                    $invoice = $this->latestInvoice;
                    if ($invoice) {
                        $link = route($route, $this->latestInvoice);
                    } else {
                        $link = route('proxypanel::manage', $this);
                    }
                }
                return '<a href="' . $link . '" class="no-underline text-xl">⚙️</a>';
            })->asHtml(),

            MorphTo::make('Owner')
                ->hideWhenUpdating(),

            Currency::make('Price', 'current_price')->asMinorUnits(),

            Text::make('Status')->readonly()->sortable(),

            HasMany::make('Invoices', 'subscription_id', Invoice::class),

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
        return [];
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

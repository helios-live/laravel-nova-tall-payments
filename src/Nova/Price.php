<?php

namespace AlexEftimie\LaravelPayments\Nova;

use Spatie\TagsField\Tags;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Actions\PriceTestServer;

use Laravel\Nova\Resource;
use \AlexEftimie\LaravelPayments\Models\Price as PriceModel;

class Price extends Resource
{
    public static $group = 'Billing';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \AlexEftimie\LaravelPayments\Models\Price::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

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
            Text::make('Product', function () {
                return $this->product->name;
            })->exceptOnForms()->asHtml(),
            BelongsTo::make('Product', 'product', Product::class)->onlyOnForms(),
            Text::make('Name')
                ->rules('required', 'max:255')
                ->asHtml(),
            Slug::make('Slug')
                ->hideFromIndex()
                ->from('Name')
                ->separator('-')
                ->rules('required', 'max:255')
                ->creationRules('unique:products,slug')
                ->updateRules('unique:products,slug,{{resourceId}}'),
            Currency::make('Amount')->asMinorUnits(),
            Code::make('Payload')->language('json')->json(),
            Select::make('Billing Period')->options(PriceModel::$period_map[1]),
            Tags::make('Server Tags', 'Tags'),
            Number::make('Order')
                ->default(0)
                ->sortable(),
            Boolean::make('Status')
                ->withMeta(["value" => 1]),
            HasMany::make('Subscriptions'),
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
            new PriceTestServer,
        ];
    }
}

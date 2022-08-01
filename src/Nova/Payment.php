<?php

namespace IdeaToCode\LaravelNovaTallPayments\Nova;

use Carbon\Carbon;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use IdeaToCode\Nova\Fields\Accounting\Accounting;

class Payment extends Resource
{
    public static $group = 'Billing';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \IdeaToCode\LaravelNovaTallPayments\Models\Payment::class;

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



            DateTime::make("Created", "created_at")
                ->displayUsing(function ($date) {
                    return optional($date)->format('Y-m-d');
                })
                ->onlyOnIndex()
                ->sortable()
                ->filterable(),

            DateTime::make("Created At")->default(function () {
                return Carbon::now();
            })
                ->displayUsing(function ($date) {
                    return optional($date)->format('Y-m-d H:i:s');
                })
                ->hideFromIndex(),


            // Currency
            Accounting::make('Amount')
                ->rules('required')
                ->hideWhenUpdating()
                ->asMinorUnits(),

            BelongsTo::make('Invoice', 'invoice', Invoice::class)
                ->hideWhenUpdating(),

            Code::make('Gateway')->json(),

            Text::make('Gateway', function () {
                return $this->resource->gateway->Name ?? '-';
            })->exceptOnForms(),

            Text::make('ID', function () {
                return $this->resource->gateway->EID ?? '-';
            })->exceptOnForms()
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
        return [];
    }
}

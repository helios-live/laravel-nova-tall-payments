<?php

namespace AlexEftimie\LaravelPayments\Nova;

use App\Nova\Team;
use Carbon\Carbon;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use App\Nova\Traits\ReferralTrait;
use Laravel\Nova\Fields\BelongsTo;

class Invoice extends Resource
{
    use ReferralTrait;

    public static $group = 'Billing';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \AlexEftimie\LaravelPayments\Models\Invoice::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
        $ref = $this->getReferralModel();
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Currency::make('Amount')->asMinorUnits()
                ->default(function () use ($ref) {
                    if ($ref) {
                        return $ref->current_price;
                    }
                })
                ->readonly(function () {
                    return $this->resource->exists;
                }),
            DateTime::make('Created At', 'created_at')->default(function () {
                return Carbon::now();
            }),
            DateTime::make('Due At', 'due_at')->default(function () {
                return Carbon::parse('+24 hours');
            }),
            Text::make('Status', function () {
                return view('nova.partials.invoice-status', [
                    'status' => $this->status,
                ])->render();
            })->asHtml(),
            $this->ownerField($ref),
            BelongsTo::make('Subscription')
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->nullable(),
            Text::make('Subscription', function () {
                return $this->subscription->name;
            })->exceptOnForms()->asHtml(),
            HasMany::make('Payments', 'subscription_id', Payment::class),
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
            new \App\Nova\Actions\RefundPayment,
        ];
    }


    protected function ownerField($ref)
    {

        $meta = [];
        if ($ref) {
            $meta = [
                'morphToId' => $ref->owner->id,
                'morphToType' => $ref->owner->getTable(),
            ];
        }
        $ownerField = MorphTo::make('Owner')
            ->withMeta($meta)
            ->hideWhenUpdating()
            // ->exceptOnForms()
            ->types([
                Team::class,
            ]);

        return $ownerField;
    }
}

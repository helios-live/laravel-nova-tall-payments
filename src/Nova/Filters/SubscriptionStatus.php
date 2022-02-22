<?php

namespace AlexEftimie\LaravelPayments\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class SubscriptionStatus extends Filter
{
    public $name = 'Status';
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if (is_null($value)) {
            return $query;
        }
        if ($value == 'active_and_new') {
            return $query->where(function ($query) {
                return $query->where('status', 'new')
                    ->orWhere('status', 'active');
            });
        }
        return $query->where('status', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Active & New' => 'active_and_new',
            'Active' => 'active',
            'Ended' => 'ended',
            'New' => 'new',
            'All' => null,
        ];
    }
    /**
     * The default value of the filter.
     *
     * @var string
     */
    public function default()
    {
        return 'active_and_new';
    }
}
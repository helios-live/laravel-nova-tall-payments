<?php

namespace AlexEftimie\LaravelPayments\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static $period_map = [
        [   '1d' => '1 day',
            '1w' => '1 week',
            '1m' => '1 month',
            '1q' => '3 months',
            '1y' => '1 year'
        ]
    ];

    public function getRouteKeyName() { return 'slug'; }

    public function product(){ return $this->belongsTo(Product::class); }

    /**
     * priceWithDiscount
     * @param Coupon $coupon
     * @return amount in cents
     */
    public function priceWithDiscount(Coupon $coupon = null) {
        $base_price = $this->amount;

        if (is_null($coupon)) {
            return $base_price;
        }

        $discount = $coupon->discount;

        if ( $discount->type == Coupon::TYPE_FIXED ) {
            $discounted_price = $base_price - $coupon->amount;
        } else if ($discount->type == Coupon::TYPE_PERCENTAGE ) {
            $discounted_price = $base_price * $coupon->amount / 100;
        }

        return max(0, $discounted_price);
    }

    public function getNextPeriodFrom(Carbon $date)
    {
        return $date->add(Price::$period_map[0][ $this->billing_period ]);
    }


}

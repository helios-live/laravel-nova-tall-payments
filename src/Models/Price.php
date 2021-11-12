<?php

namespace AlexEftimie\LaravelPayments\Models;

use Carbon\Carbon;
use Spatie\Tags\HasTags;
use AlexEftimie\LaravelPayments\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * AlexEftimie\LaravelPayments\Models\Price
 *
 * @property int $id
 * @property int $product_id
 * @property string $slug
 * @property string $name
 * @property int $amount
 * @property string $billing_period
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AlexEftimie\LaravelPayments\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereBillingPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Price extends Model
{
    use HasFactory;
    use HasTags;

    protected $guarded = [];

    protected $prevent_delete = ['subscriptions'];

    public static $period_map = [
        [
            '1d' => '1 day',
            '1w' => '1 week',
            '1m' => '1 month',
            '1q' => '3 months',
            '1y' => '1 year'
        ],
        [
            '1d' => 'daily',
            '1w' => 'weekly',
            '1m' => 'monthly',
            '1q' => 'quarterly',
            '1y' => 'yearly'
        ]
    ];


    protected $casts = [
        'payload' => 'object',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    public function scopeActive($query)
    {
        return $query->where('status', '=', '1');
    }

    /**
     * priceWithDiscount
     * @param Coupon $coupon
     * @return amount in cents
     */
    public function priceWithDiscount(Coupon $coupon = null)
    {
        $base_price = $this->amount;

        if (is_null($coupon)) {
            return $base_price;
        }

        $discount = $coupon->discount;

        if ($discount->type == Coupon::TYPE_FIXED) {
            $discounted_price = $base_price - $coupon->amount;
        } else if ($discount->type == Coupon::TYPE_PERCENTAGE) {
            $discounted_price = $base_price * $coupon->amount / 100;
        }

        return max(0, $discounted_price);
    }

    public function getNextPeriodFrom(Carbon $date)
    {
        return $date->add(Price::$period_map[0][$this->billing_period]);
    }

    public function getPeriodAttribute()
    {
        return Price::$period_map[1][$this->billing_period];
    }
}

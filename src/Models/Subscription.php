<?php

namespace AlexEftimie\LaravelPayments\Models;

use AlexEftimie\LaravelPayments\Billable;
use AlexEftimie\LaravelPayments\Events\SubscriptionCreated;
use Appstract\Meta\Metable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
Subscription example: 
    $team = Team::first();
    $price = Price::whereSlug('rp-airship-monthly')->first();
    $sub = Subscription::NewSubscription($team, $price, null);
*/
class Subscription extends Model
{
    use Metable;
    use HasFactory;
    
    public const REASON_EXPIRED = "Expired";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function price() { return $this->belongsTo(Price::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }

    public static function NewSubscription(Billable $owner, Price $price, Coupon $coupon = null) {

        // TODO: Check if discount still has valid usages per user
        // TODO: Check if discount still has valid usages total
        $coupon_data = null;
        if ( ! is_null($coupon)) {
            $coupon_data = [
                'code' => $coupon->code,
                'discount' => $coupon->discount,
            ];
        }
        $sub = (new Subscription)->fill([
            'owner_id' => $owner->getKey(),
            'price_id' => $price->id,
            'current_price' => $price->priceWithDiscount($coupon),
            'base_price' => $price->amount,
            'coupon' => $coupon_data,
            'expires_at' => null,
            'status' => 'New',
        ]);

        $sub->save();

        event(new SubscriptionCreated($sub));

        return $sub;
    }

    public function cancel()
    {
        $this->status = 'Canceled';
        $this->save();

    }

    public function end($reason)
    {
        $this->addOrUpdateMeta('end_reason', $reason);
        $this->status = 'Ended';
        $this->save();
    }
}

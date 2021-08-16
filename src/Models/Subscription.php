<?php

namespace AlexEftimie\LaravelPayments\Models;

use App\Models\Team;
use Appstract\Meta\Metable;
use Laravel\Nova\Actions\Actionable;
use AlexEftimie\LaravelPayments\Models\Model;
use AlexEftimie\LaravelPayments\Models\Price;
use AlexEftimie\LaravelPayments\Models\Coupon;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Contracts\Billable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AlexEftimie\LaravelPayments\Events\SubscriptionEnded;
use AlexEftimie\LaravelPayments\Events\SubscriptionCreated;
use AlexEftimie\LaravelPayments\Events\SubscriptionStarted;
use AlexEftimie\LaravelPayments\Events\SubscriptionCanceled;
use AlexEftimie\LaravelPayments\Events\SubscriptionEndedEvent;
use AlexEftimie\LaravelPayments\Events\SubscriptionInitFailed;

/*
Subscription example: 
    $team = Team::first();
    $price = AlexEftimie\LaravelPayments\Models\Price::whereSlug('rp-airplane-monthly')->first();
    $sub = AlexEftimie\LaravelPayments\Models\Subscription::NewSubscription($team, $price, null);
*/
/**
 * AlexEftimie\LaravelPayments\Models\Subscription
 *
 * @property int $id
 * @property int $owner_id
 * @property int $price_id
 * @property int $current_price
 * @property int $base_price
 * @property mixed|null $coupon
 * @property string $status
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\AlexEftimie\LaravelPayments\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Appstract\Meta\Meta[] $meta
 * @property-read int|null $meta_count
 * @property-read \AlexEftimie\LaravelPayments\Models\Price $price
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCurrentPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use Metable;
    use Actionable;
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

    public function latestInvoice() { return $this->hasOne(Invoice::class)->latestOfMany(); }
    public function owner() { return $this->morphTo(); }
    public function price() { return $this->belongsTo(Price::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function affiliate() { return $this->morphTo(); }
    
    public function getNameAttribute() {
        return $this->price->product->name . " - " . $this->price->name . " #" . $this->id;
    }
    public static function NewSubscription($manager, Billable $owner, Price $price, Coupon $coupon = null) {

        // TODO: Check if discount still has valid usages per user
        // TODO: Check if discount still has valid usages total
        $coupon_data = null;
        if ( ! is_null($coupon)) {
            $coupon_data = [
                'code' => $coupon->code,
                'discount' => $coupon->discount,
            ];
        }
        $aff_data = [];
        if ( !is_null($owner->affiliate) ) {
            $aff_data = [
                'affiliate_id' => $owner->affiliate->getKey(),
                'affiliate_type' => get_class($owner->affiliate),
            ];
        }
        $sub = $owner->subscriptions()->create([
            'manager' => $manager,
            'price_id' => $price->id,
            'current_price' => $price->priceWithDiscount($coupon),
            'base_price' => $price->amount,
            'coupon' => $coupon_data,
            'expires_at' => null,
            'status' => 'New',
        ] + $aff_data);

        $sub->save();

        event(new SubscriptionCreated($sub));

        return $sub;
    }

    public function start()
    {

        $this->forceFill(['status' => 'Active'])->save();
        $m = app($this->manager);

        if ( !$m->initSubscription($this) ) {
            event(new SubscriptionInitFailed($this));
            return null;
        }

        $start_event = new SubscriptionStarted($this);
        event($start_event);
    }

    public function cancel()
    {
        $this->status = 'Canceled';
        $this->save();

        event(new SubscriptionCanceled($this));
    }

    public function end($reason)
    {
        $this->addOrUpdateMeta('end_reason', $reason);
        $this->status = 'Ended';
        $this->save();

        event(new SubscriptionEnded($this));
    }

    public function scopeActive($query) {
        return $query->where('status', 'Active');
    }
    public function isActive() {
        return $this->status == 'Active';
    }

    public function isOff() {
        return $this->status == 'Ended';
    }
}

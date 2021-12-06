<?php

namespace AlexEftimie\LaravelPayments\Models;

use Carbon\Carbon;
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
use AlexEftimie\LaravelPayments\Events\SubscriptionNewInvoice;

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
 * @property \AlexEftimie\LaravelPayments\Models\Invoice|null $latestInvoice
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
        'payload' => 'object',
    ];

    public function latestInvoice()
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }
    public function owner()
    {
        return $this->morphTo();
    }
    public function price()
    {
        return $this->belongsTo(Price::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function affiliate()
    {
        return $this->morphTo();
    }

    public function getNameAttribute()
    {
        return $this->price->product->name . " - " . $this->price->name . " #" . $this->id;
    }
    public static function NewSubscription($manager, Billable $owner, Price $price, Coupon $coupon = null)
    {

        // TODO: Check if discount still has valid usages per user
        // TODO: Check if discount still has valid usages total
        $coupon_data = null;
        if (!is_null($coupon)) {
            $coupon_data = [
                'code' => $coupon->code,
                'discount' => $coupon->discount,
            ];
        }
        $aff_data = [];
        if (!is_null($owner->affiliate)) {
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
            'payload' => $price->payload,
        ] + $aff_data);

        $sub->save();

        event(new SubscriptionCreated($sub));

        return $sub;
    }

    public function start()
    {

        $this->forceFill(['status' => 'Active'])->save();
        $m = app($this->manager);

        if (!$m->initSubscription($this)) {
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

    /**
     * @return bool true if the subscription is active and there are no invoices(new subscriptions)
     * @return bool true for daily packages if the last invoice is paid and there's less than 24 hours left
     * @return bool true for weekly packages if the last invoice is paid and there's less than 48 hours left
     * @return bool true for monthly packages if the last invoice is paid and there's less than 72 hours left
     * @return bool true for yearly packages if the last invoice is paid and there's less than 7 days left
     * @return bool false otherwise
     */
    public function mustIssueNewInvoice(bool $emitEvent = true)
    {
        if ($emitEvent) {
            $result = $this->mustIssueNewInvoice(false);
            if ($result) {
                event(new SubscriptionNewInvoice($this));
            }
            return $result;
        }
        $lastInvoice = $this->latestInvoice;

        $active = $this->isActive();

        if (!$active) {
            return false;
        }

        // subscription has no invoices
        if ($lastInvoice == null) {
            return true;
        }

        // we already have one, no need for a new one
        if (in_array($lastInvoice->status, ['due', 'overdue'])) {
            return false;
        }

        // the latest invoice is paid, if the remaining time is less than window
        // issue new invoice
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return true;
        }
        $expireDays = (int)(Carbon::now()->diff($this->expires_at))->format('%a');
        $billingPeriod = $this->price->billing_period;

        switch ($billingPeriod) {
            case '1d':
                return $expireDays < 1;
            case '1w':
                return $expireDays < 2;
            case '1m':
                return $expireDays < 3;
            case '1y':
                return $expireDays < 7;

            default:
                throw new \Exception("Invalid Billing Period: " . $billingPeriod);
        }
    }

    public function end($reason)
    {
        $this->addOrUpdateMeta('end_reason', $reason);
        $this->status = 'Ended';
        $this->save();

        event(new SubscriptionEnded($this));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    public function isActive()
    {
        return $this->status == 'Active';
    }

    public function isOff()
    {
        return $this->status == 'Ended';
    }

    public function getPayload()
    {
        return $this->payload ?? $this->price->payload;
    }
}

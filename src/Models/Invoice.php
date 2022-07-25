<?php

namespace IdeaToCode\LaravelNovaTallPayments\Models;

use Carbon\Carbon;
use Appstract\Meta\Metable;
use Illuminate\Support\Str;
use IdeaToCode\LaravelNovaTallPayments\Models\Model;
use IdeaToCode\LaravelNovaTallPayments\Models\Payment;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoicePaid;
use IdeaToCode\LaravelNovaTallPayments\Models\Subscription;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use IdeaToCode\LaravelNovaTallPayments\Events\InvoiceRefunded;
use IdeaToCode\LaravelNovaTallPayments\Events\PayingInvoice;
use IdeaToCode\LaravelNovaTallPayments\Events\SubscriptionStarted;
use Laravel\Nova\Actions\Actionable;

/**
 * IdeaToCode\LaravelNovaTallPayments\Models\Invoice
 *
 * @property int $id
 * @property int $subscription_id
 * @property int $amount
 * @property mixed|null $plus
 * @property \Illuminate\Support\Carbon $due_at
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\IdeaToCode\LaravelNovaTallPayments\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \IdeaToCode\LaravelNovaTallPayments\Models\Subscription $subscription
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePlus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use HasFactory;
    use Metable;
    use Actionable;

    protected $casts = [
        'due_at' => 'datetime',
    ];
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => InvoiceCreated::class,
    ];
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function owner()
    {
        return $this->morphTo();
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }


    public function getStatusAttribute()
    {

        if (!is_null($this->refund_id)) {
            return 'refunded';
        }

        if (!is_null($this->payment_id)) {
            return 'paid';
        }

        if (Carbon::now() > $this->due_at) {
            return 'overdue';
        }

        return 'due';
    }
    public function getNameAttribute()
    {
        if ($this->subscription_id) {
            return $this->subscription->name;
        }
        return $this->owner->name;
    }

    public function getDescription()
    {

        // if the invoice has a description set, use that
        if (!is_null($this->plus) && isset($this->plus->description)) {
            return $this->plus->description;
        }

        // if the invoice is part of a subscription, use that name
        if (!is_null($this->subscription)) {
            return 'Subscription Payment: ' . $this->subscription->id;
        }

        // return a generic name
        return 'Invoice ' . $this->uuid;
    }


    public function pay(string $gateway, string $id)
    {


        $p = $this->payments()->create([
            'amount' => $this->amount,
            'gateway' => [
                'Name' => $gateway,
                'EID' => $id,
            ]
        ]);

        $this->forceFill([
            'payment_id' => $p->id,
            'refund_id' => null,
        ])->save();


        $event = new PayingInvoice($this);
        $event->setGateway($gateway, $id);
        event($event);
    }

    public function refund(string $gateway, string $id, $amount = null)
    {
        if (is_null($amount) || $amount > $this->amount) {
            $amount = $this->amount;
        }
        $p = $this->payments()->create([
            'amount' => -1 * $amount,
            'gateway' => [
                'Name' => $gateway,
                'EID' => $id,
            ]
        ]);

        $this->forceFill([
            'refund_id' => $p->id,
        ])->save();


        $event = new InvoiceRefunded($this);
        $event->setGateway($gateway, $id);
        event($event);

        $this->subscription->end('Refunded');
    }

    public static function newUuid()
    {
        return (string) Str::orderedUuid();
    }
}
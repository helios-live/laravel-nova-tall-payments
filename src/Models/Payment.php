<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use AlexEftimie\LaravelPayments\Models\Model;

/**
 * AlexEftimie\LaravelPayments\Models\Payment
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $amount
 * @property int|null $refund_for
 * @property mixed $gateway
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AlexEftimie\LaravelPayments\Models\Invoice $invoice
 * @property-read Payment|null $refundFor
 * @property-read Payment|null $refundPayment
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRefundFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];
	protected $casts = [
		'gateway' => 'object',
	];    
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function refundFor() { return $this->belongsTo(Payment::class, 'refund_for'); }
    public function refundPayment() { return $this->hasOne(Payment::class, 'refund_for'); }
}

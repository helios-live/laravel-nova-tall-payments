<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function refundFor() { return $this->belongsTo(Payment::class, 'refund_for'); }
    public function refundPayment() { return $this->hasOne(Payment::class, 'refund_for'); }
}

<?php

namespace AlexEftimie\LaravelPayments\Models;

use AlexEftimie\LaravelPayments\Events\InvoiceCreated;
use AlexEftimie\LaravelPayments\Events\InvoicePaid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $casts = [
        'due_at' => 'datetime',
    ];
    protected $fillable = [
        'amount',
        'due_at',
        'paid_at',
        'plus',
    ];

    protected $dispatchesEvents = [
        'created' => InvoiceCreated::class,
    ];
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function pay() {
        // TODO: Payment gateway
        // TODO: Create Payment
        $this->paid_at = Carbon::now();
        $this->save();

        event(new InvoicePaid($this));
    }


}

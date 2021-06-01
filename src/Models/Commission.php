<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use AlexEftimie\LaravelPayments\Models\Model;

class Commission extends Model
{
	protected $fillable = [
		'invoice_id', 'amount'
	];
	public function invoice() { return $this->belongsTo(Invoice::class); }
	
    public function owner() { return $this->morphTo(); }
}
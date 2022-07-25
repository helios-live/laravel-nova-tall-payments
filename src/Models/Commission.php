<?php

namespace IdeaToCode\LaravelNovaTallPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use IdeaToCode\LaravelNovaTallPayments\Models\Model;

class Commission extends Model
{
	protected $fillable = [
		'invoice_id', 'amount'
	];
	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function owner()
	{
		return $this->morphTo();
	}
}

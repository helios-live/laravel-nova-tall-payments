<?php

namespace AlexEftimie\LaravelPayments\Traits;

use App\Models\User;

trait HasAffiliate {
	public function affiliate() { return $this->belongsTo(User::class, 'affiliate_id'); }
}
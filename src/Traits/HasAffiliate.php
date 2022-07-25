<?php

namespace IdeaToCode\LaravelNovaTallPayments\Traits;

use App\Models\User;

trait HasAffiliate
{
	public function affiliate()
	{
		return $this->belongsTo(User::class, 'affiliate_id');
	}
}

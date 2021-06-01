<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use AlexEftimie\LaravelPayments\Models\Model;

class CommissionPayment extends Model
{	
    public function owner() { return $this->morphTo(); }
}
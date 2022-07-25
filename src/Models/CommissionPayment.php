<?php

namespace IdeaToCode\LaravelNovaTallPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use IdeaToCode\LaravelNovaTallPayments\Models\Model;

class CommissionPayment extends Model
{
    public function owner()
    {
        return $this->morphTo();
    }
}

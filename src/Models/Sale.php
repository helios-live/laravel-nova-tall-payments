<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'team_id',
        'product_id',
        'price_id',
    ];


    public function scopePayments($query)
    {
        return $query->where('amount', '>=', 0);
    }
    public function scopeRefunds($query)
    {
        return $query->where('amount', '<', 0);
    }
}
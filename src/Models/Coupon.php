<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    const TYPE_FIXED = "fixed";
    const TYPE_PERCENTAGE = "percentage";
    public function getRouteKeyName() { return 'code'; }
    public function getKeyName() { return 'code'; }
}

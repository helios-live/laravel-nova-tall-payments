<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $with = ['prices'];

    public function getRouteKeyName() { return 'slug'; }

    public function prices() { return $this->hasMany(Price::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function category() { return $this->belongsTo(Category::class); }

}

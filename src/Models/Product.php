<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use AlexEftimie\LaravelPayments\Models\Model;

/**
 * AlexEftimie\LaravelPayments\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AlexEftimie\LaravelPayments\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\AlexEftimie\LaravelPayments\Models\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AlexEftimie\LaravelPayments\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    public $with = ['prices'];

    protected $casts = [
        'features' => 'array'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function prices()
    {
        return $this->hasMany(Price::class)->orderBy('order', 'desc');
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', '1');
    }
}

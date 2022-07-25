<?php

namespace IdeaToCode\LaravelNovaTallPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use IdeaToCode\LaravelNovaTallPayments\Models\Model;

/**
 * IdeaToCode\LaravelNovaTallPayments\Models\Coupon
 *
 * @property int $code
 * @property mixed $discount
 * @property mixed $usage
 * @property mixed $valid_categories_ids
 * @property mixed $valid_products_ids
 * @property string $starts_at
 * @property string|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereValidCategoriesIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereValidProductsIds($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use HasFactory;
    const TYPE_FIXED = "fixed";
    const TYPE_PERCENTAGE = "percentage";
    public function getRouteKeyName()
    {
        return 'code';
    }
    public function getKeyName()
    {
        return 'code';
    }
}

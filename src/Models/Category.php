<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function getRouteKeyName() { return 'slug'; }

    public function parent() { return $this->belongsTo(Category::class, 'parent_category_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_category_id'); }
}

<?php

namespace AlexEftimie\LaravelPayments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AlexEftimie\LaravelPayments\Models\Log
 *
 * @property int $id
 * @property string $parent_type
 * @property int $parent_id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereParentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereValue($value)
 * @mixin \Eloquent
 */
class Log extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'value' => 'object',
    ];

    public function parent() { return $this->morphTo(); }

    public static function add($owner, $key, $value)
    {
        $l = new Log();
        $l->fill([
            'name' => $key,
            'value' => $value,
        ]);
        $owner->logs()->save($l);
    }
}

<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Models;

use IdeaToCode\LaravelNovaTallPaymentsayments\Traits\Loggable;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use Loggable;
    protected $prevent_delete = [];

    protected static function boot()
    {
        parent::boot();


        static::deleting(function ($elem) {
            $relationMethods = $elem->prevent_delete;

            foreach ($relationMethods as $relationMethod) {
                if ($elem->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });
    }
}
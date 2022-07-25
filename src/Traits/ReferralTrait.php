<?php

namespace IdeaToCode\LaravelNovaTallPayments\Traits;

use ReflectionProperty;
use Illuminate\Support\Str;


trait ReferralTrait
{
    public function getReferralModel()
    {
        $r  = request()->get('viaResource');
        $i = (int)request()->get('viaResourceId');
        if ($r && $i) {
            // $m = app(Str::singular($r));
            $className = 'App\\Nova\\' . Str::studly(Str::singular($r));
            if (class_exists($className)) {

                $prop = new ReflectionProperty($className, 'model');

                $modelClass = $prop->getValue();

                $model = app($modelClass);
                return $model->find($i);
            }
        }
        return null;
    }
}
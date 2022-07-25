<?php

namespace IdeaToCode\LaravelNovaTallPayments\Facades;

use Illuminate\Support\Facades\Facade;

class Larapay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'larapay';
    }
}

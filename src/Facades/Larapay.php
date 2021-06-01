<?php

namespace AlexEftimie\LaravelPayments\Facades;

use Illuminate\Support\Facades\Facade;

class Larapay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'larapay';
    }
}

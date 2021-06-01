<?php

namespace AlexEftimie\LaravelPayments\Traits;

use AlexEftimie\LaravelPayments\Models\Log;


trait Loggable
{
    public function logs()
    {
        return $this->morphMany(Log::class, 'parent');
    }
}

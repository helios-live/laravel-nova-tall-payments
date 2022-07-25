<?php

namespace IdeaToCode\LaravelNovaTallPayments\Traits;

use IdeaToCode\LaravelNovaTallPayments\Models\Log;


trait Loggable
{
    public function logs()
    {
        return $this->morphMany(Log::class, 'parent');
    }
}
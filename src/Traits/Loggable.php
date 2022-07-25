<?php

namespace IdeaToCode\LaravelNovaTallPaymentsayments\Traits;

use IdeaToCode\LaravelNovaTallPaymentsayments\Models\Log;


trait Loggable
{
    public function logs()
    {
        return $this->morphMany(Log::class, 'parent');
    }
}
<?php

namespace AlexEftimie\LaravelPayments;

use Illuminate\Support\ServiceProvider;

interface Billable
{
    public function getKey();
    /**
     */
}
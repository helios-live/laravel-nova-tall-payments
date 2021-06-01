<?php

namespace AlexEftimie\LaravelPayments\Traits;


trait HasAdmin
{
    function isAdmin() {
        // $id = $this->key
        
        return in_array( $this->getKey(), config('app.admins'));
    }
}

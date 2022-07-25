<?php

namespace IdeaToCode\LaravelNovaTallPayments\Traits;


trait HasAdmin
{
    public function isAdmin()
    {
        // $id = $this->key

        return in_array($this->getKey(), config('app.admins'));
    }

    public function isSuperAdmin()
    {
        return $this->email == 'office@alexeftimie.ro';
    }
}

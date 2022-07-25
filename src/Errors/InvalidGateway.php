<?php

namespace IdeaToCode\LaravelNovaTallPayments\Errors;


class InvalidGateway extends \Exception
{
    protected $message = "Invalid Payment Gateway";
}

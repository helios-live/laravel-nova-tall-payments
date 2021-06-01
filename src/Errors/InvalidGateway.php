<?php

namespace AlexEftimie\LaravelPayments\Errors;


class InvalidGateway extends \Exception {
    protected $message = "Invalid Payment Gateway";
}
<?php

namespace AlexEftimie\LaravelPayments\Errors;


class InvoiceAlreadyPaid extends \Exception {
    protected $message = "Invoice Already Paid";
}
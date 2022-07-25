<?php

namespace IdeaToCode\LaravelNovaTallPayments\Errors;


class InvoiceAlreadyPaid extends \Exception
{
    protected $message = "Invoice Already Paid";
}
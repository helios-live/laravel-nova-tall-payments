<?php


namespace AlexEftimie\LaravelPayments\Tests\Models;

use AlexEftimie\LaravelPayments\Billable;
use AlexEftimie\LaravelPayments\BillableTrait;
use Illuminate\Foundation\Auth\User as BaseUser;


class User extends BaseUser implements Billable {
	use BillableTrait;
}
<?php


namespace IdeaToCode\LaravelNovaTallPayments\Tests\Models;

use IdeaToCode\LaravelNovaTallPayments\Billable;
use IdeaToCode\LaravelNovaTallPayments\BillableTrait;
use Illuminate\Foundation\Auth\User as BaseUser;


class User extends BaseUser implements Billable {
	use BillableTrait;
}
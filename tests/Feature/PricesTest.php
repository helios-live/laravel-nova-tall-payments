<?php

namespace IdeaToCode\LaravelNovaTallPayments\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use IdeaToCode\LaravelNovaTallPayments\Models\Price;
use IdeaToCode\LaravelNovaTallPayments\Tests\FeatureTestCase;


class PricesTest extends FeatureTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->SetupSubscription();
    }

    public function test_getNextPeriodFrom()
    {
        $periods = Price::$period_map[0];

        foreach ($periods as $key => $value) {


            // test period
            $this->price->billing_period = $key;

            $expected = Carbon::now()->add($value)->format('Y-m-d H:i:s');
            $actual = $this->price->getNextPeriodFrom(Carbon::now())->format('Y-m-d H:i:s');
            $this->assertEquals($expected, $actual, "getNextPeriodFrom " . $key . " not equal to " . $value);
        }
    }
}

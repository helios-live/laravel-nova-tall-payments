<?php
namespace AlexEftimie\LaravelPayments\Tests\Browser;

use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Schema;
use AlexEftimie\LaravelPayments\Models\Invoice;
use AlexEftimie\LaravelPayments\Tests\BrowserTestCase;

class StripeCardTest extends BrowserTestCase
{
    public function test_it_runs_migrations() {
        $sch = Schema::getColumnListing('prices');
        
        $this->assertNotEquals(count($sch), 0);
    }

    public function test_stripe_single_charge()
    {
            // $this->artisan('migrate:fresh', ['--seed' => true]);
        $this->SetupSubscription();


        $this->browse(function (Browser $browser) {

            $invoice = $this->invoice;

            $this->assertNotEmpty($invoice->uuid);

            echo "\nGoing to: /test/" . $invoice->uuid . "\n";
            $browser->visit('/test/' . $invoice->uuid);

            // $browser->assertAttribute('@gateway-stripe', 'dusk', 'gateway-stripe');

            $linkText = 'Stripe';

            if ($browser->waitForLink($linkText)) {
                $browser->clickLink($linkText);
            } else {
                $this->fail("Stripe Gateway Not Present");
            }
                    // ->type('#card-element .CardNumberField-input-wrapper input', '4242 4242 4242 4242')
            $browser->waitFor('iframe[title="Secure card payment input frame"]')
                    ->withinFrame('iframe[title="Secure card payment input frame"]', function($browser){
                        $browser->waitFor('input[placeholder="Card number"]', 1000);
                        $browser->keys('input[placeholder="Card number"]', '4242 4242 4242 4242')
                            ->keys('input[placeholder="MM / YY"]', '0122')
                            ->keys('input[placeholder="CVC"]', '123')
                            ->keys('input[placeholder="ZIP"]', '222222');
                    });

            $browser->press('button[type="submit"')
                    ->waitUntilMissing('iframe[title="Secure card payment input frame"]');

            $browser->assertSee('Successful');

//             // reset db2_autocommit(connection)
        });
    }
}

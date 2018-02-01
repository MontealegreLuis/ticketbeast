<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Stripe\Account;
use Tests\DuskTestCase;

class ConnectWithStripeTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @throws \Throwable
     */
    function connecting_a_stripe_account_successfully()
    {
        $promoter = factory(User::class)->create([
            'stripe_account_id' => null,
            'stripe_access_token' => null,
        ]);

        $this->browse(function (Browser $browser) use ($promoter) {
            $browser
                ->loginAs($promoter)
                ->visit('/backstage/stripe-connect/connect')
                ->clickLink('Connect with Stripe')
                ->assertUrlIs('https://connect.stripe.com/oauth/authorize')
                ->assertQueryStringHas('response_type', 'code')
                ->assertQueryStringHas('scope', 'read_write')
                ->assertQueryStringHas('client_id', config('services.stripe.client_id'))
                ->clickLink('Skip this account form')
                ->assertRouteIs('backstage.concerts.index')
            ;

            $promoterConnectedToStripe = $promoter->fresh();

            $this->assertNotNull($promoterConnectedToStripe->stripe_account_id);
            $this->assertNotNull($promoterConnectedToStripe->stripe_access_token);

            $connectedAccount = Account::retrieve(null, [
                'api_key' => $promoterConnectedToStripe->stripe_access_token,
            ]);
            $this->assertEquals($connectedAccount->id, $promoterConnectedToStripe->stripe_account_id);
        });
    }
}

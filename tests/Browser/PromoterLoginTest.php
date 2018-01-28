<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PromoterLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @throws \Throwable */
    public function test_promoter_logs_in_successfully()
    {
        factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/login')
                ->type('email', 'jane@example.com')
                ->type('password', 'super-secret-password')
                ->press('Log in')
                ->assertPathIs('/backstage/concerts')
            ;
        });
    }

    /** @throws \Throwable */
    public function test_promoter_logs_with_invalid_credentials()
    {
        factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser
                ->logout()  // If removed it thinks it's already logged in from the previous test
                ->visit('/login')
                ->type('email', 'jane@example.com')
                ->type('password', 'wrong-password')
                ->press('Log in')
                ->assertPathIs('/login')
                ->assertInputValue('email', 'jane@example.com')
                ->assertSee('credentials do not match')
            ;
        });
    }
}

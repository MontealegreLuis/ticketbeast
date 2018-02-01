<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature\Backstage;

use App\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoterLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function logging_in_with_valid_credentials()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password',
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    function logging_in_with_invalid_credentials()
    {
        factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);
        $this->from('/login');

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_in_without_an_account()
    {
        $this->from('/login');

        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertFalse(Auth::check());
    }
}

<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature;

use App\Invitation;
use App\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function viewing_an_unused_invitation()
    {
        $this->withoutExceptionHandling();

        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(200);
        $response->assertViewIs('invitations.show');

        $this->assertTrue($response->original->getData()['invitation']->is($invitation));
    }

    /** @test */
    function viewing_a_used_invitation()
    {
         factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(404);
    }

    /** @test */
    function viewing_an_invitation_that_does_not_exist()
    {
        $response = $this->get('/invitations/TESTCODE1234');

        $response->assertStatus(404);
    }

    /** @test */
    function registering_with_a_valid_invitation_code()
    {
        $this->withoutExceptionHandling();

        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertTrue($invitation->fresh()->user->is($user));
    }

    /** @test */
    function registering_with_a_used_invitation_code()
    {
        factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'code' => 'TESTCODE1234',
        ]);
        $this->assertEquals(1, User::count());

        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertStatus(404);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    function registering_with_an_invitation_code_that_does_not_exist()
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, User::count());
    }
}

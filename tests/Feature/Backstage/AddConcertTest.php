<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature\Backstage;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_add_concerts()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    function guests_cannot_add_concerts()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}

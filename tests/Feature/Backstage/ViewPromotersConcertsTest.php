<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature\Backstage;


use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPromotersConcertsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_view_a_promoters_concerts()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_view_their_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concerts = factory(Concert::class, 3)->create(['user_id' =>  $user->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);
        $concertsInView = $response->original->getData()['concerts'];
        $this->assertTrue($concertsInView->contains($concerts[0]));
        $this->assertTrue($concertsInView->contains($concerts[1]));
        $this->assertTrue($concertsInView->contains($concerts[2]));
    }
}

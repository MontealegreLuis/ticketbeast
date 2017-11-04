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
    function promoters_can_only_view_their_own_concerts()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();
        $anotherPromoter = factory(User::class)->create();

        $concertA = factory(Concert::class)->create(['user_id' =>  $promoter->id]);
        $anotherPromoterConcert = factory(Concert::class)->create([
            'user_id' =>  $anotherPromoter->id
        ]);
        $concertB = factory(Concert::class)->create(['user_id' =>  $promoter->id]);
        $concertC = factory(Concert::class)->create(['user_id' =>  $promoter->id]);


        $response = $this->actingAs($promoter)->get('/backstage/concerts');

        $response->assertStatus(200);
        $concertsInView = $response->original->getData()['concerts'];
        $this->assertTrue($concertsInView->contains($concertA));
        $this->assertTrue($concertsInView->contains($concertB));
        $this->assertTrue($concertsInView->contains($concertC));
        $this->assertFalse($concertsInView->contains($anotherPromoterConcert));
    }
}

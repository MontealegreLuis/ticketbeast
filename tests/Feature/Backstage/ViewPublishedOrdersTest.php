<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature\Backstage;

use App\User;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPublishedOrdersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function promoters_can_see_the_orders_for_their_own_concerts()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $promoter->id]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/published-concerts/{$concert->id}/orders")
        ;

        $response->assertStatus(200);
        $this->assertEquals('backstage.published-concerts.index', $response->original->getName());
        $this->assertTrue($response->original->getData()['concert']->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_orders_of_unpublished_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = ConcertFactory::createUnpublished(['user_id' => $promoter->id]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/published-concerts/{$concert->id}/orders")
        ;

        $response->assertStatus(404);
    }

    /** @test */
    function promoters_cannot_view_others_promoters_concerts_orders()
    {
        $promoter = factory(User::class)->create();
        $anotherPromoter = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $anotherPromoter->id]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/published-concerts/{$concert->id}/orders")
        ;

        $response->assertStatus(404);
    }

    /** @test */
    function a_guest_cannot_view_any_published_concerts()
    {
        $concert = ConcertFactory::createPublished();

        $response = $this
            ->get("/backstage/published-concerts/{$concert->id}/orders")
        ;

        $response->assertRedirect('/login');
    }

}

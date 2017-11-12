<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature\Backstage;

use App\User;
use Carbon\Carbon;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OrderFactory;
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

        $oldOrder = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('11 days ago')]);
        $recentOrder1 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('10 days ago')]);
        $recentOrder2 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('9 days ago')]);
        $recentOrder3 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('8 days ago')]);
        $recentOrder4 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('7 days ago')]);
        $recentOrder5 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('6 days ago')]);
        $recentOrder6 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('5 days ago')]);
        $recentOrder7 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('4 days ago')]);
        $recentOrder8 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('3 days ago')]);
        $recentOrder9 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('2 days ago')]);
        $recentOrder10 = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('1 days ago')]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/published-concerts/{$concert->id}/orders")
        ;

        $response->assertStatus(200);
        $this->assertEquals('backstage.published-concerts.index', $response->original->getName());

        $orders = $response->original->getData()['orders'];

        $this->assertFalse($orders->contains($oldOrder));
        $inOrderOrders = [
            $recentOrder10,
            $recentOrder9,
            $recentOrder8,
            $recentOrder7,
            $recentOrder6,
            $recentOrder5,
            $recentOrder4,
            $recentOrder3,
            $recentOrder2,
            $recentOrder1,
        ];
        $orders->zip($inOrderOrders)->each(function ($pair) {
            list($orderA, $orderB) = $pair;
            $this->assertTrue($orderA->is($orderB));
        });
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

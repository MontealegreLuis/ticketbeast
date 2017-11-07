<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature\Backstage;

use App\Concert;
use App\User;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishedConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_publish_their_concerts()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('unpublished')->create([
            'user_id' => $promoter->id,
            'ticket_quantity' => 10,
        ]);

        $response = $this->actingAs($promoter)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue($concert->fresh()->isPublished());
        $this->assertEquals(10, $concert->ticketsRemaining());
    }

    /** @test */
    function a_concert_can_only_be_published_once()
    {
        $promoter = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $promoter->id,
            'ticket_quantity' => 10,
        ]);

        $response = $this->actingAs($promoter)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(10, $concert->ticketsRemaining());
    }

    /** @test */
    function a_promoter_cannot_publish_other_concerts()
    {
        $promoter = factory(User::class)->create();
        $anotherPromoter = factory(User::class)->create();
        $concert = factory(Concert::class)->states('unpublished')->create([
            'user_id' => $anotherPromoter->id,
            'ticket_quantity' => 3,
        ]);
        $response = $this->actingAs($promoter)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);
        $response->assertStatus(404);
        $this->assertFalse($concert->fresh()->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function guests_cannot_publish_concerts()
    {
        $concert = factory(Concert::class)->states('unpublished')->create([
            'ticket_quantity' => 3,
        ]);
        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);
        $response->assertRedirect('/login');
        $this->assertFalse($concert->fresh()->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function concerts_that_do_not_exist_cannot_be_published()
    {
        $promoter = factory(User::class)->create();
        $response = $this->actingAs($promoter)->post('/backstage/published-concerts', [
            'concert_id' => 999,
        ]);
        $response->assertStatus(404);
    }
}

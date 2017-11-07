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

        $concertA = ConcertFactory::createPublished(['user_id' =>  $promoter->id]);
        $anotherPromoterConcert = ConcertFactory::createPublished([
            'user_id' =>  $anotherPromoter->id
        ]);
        $concertB = ConcertFactory::createPublished(['user_id' =>  $promoter->id]);
        $concertC = ConcertFactory::createPublished(['user_id' =>  $promoter->id]);

        $unpublishedConcertA = ConcertFactory::createUnpublished(['user_id' =>  $promoter->id]);
        $unpublishedConcertB = ConcertFactory::createUnpublished(['user_id' =>  $promoter->id]);
        $anotherPromoterUnpublishedConcert = ConcertFactory::createUnpublished([
            'user_id' =>  $anotherPromoter->id
        ]);
        $unpublishedConcertC = ConcertFactory::createUnpublished(['user_id' =>  $promoter->id]);


        $response = $this->actingAs($promoter)->get('/backstage/concerts');

        $response->assertStatus(200);
        $publishedConcerts = $response->original->getData()['publishedConcerts'];
        $this->assertTrue($publishedConcerts->contains($concertA));
        $this->assertTrue($publishedConcerts->contains($concertB));
        $this->assertTrue($publishedConcerts->contains($concertC));
        $this->assertFalse($publishedConcerts->contains($anotherPromoterConcert));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcertA));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcertC));
        $this->assertFalse($publishedConcerts->contains($anotherPromoterUnpublishedConcert));

        $unpublishedConcerts = $response->original->getData()['unpublishedConcerts'];
        $this->assertFalse($unpublishedConcerts->contains($concertA));
        $this->assertFalse($unpublishedConcerts->contains($concertB));
        $this->assertFalse($unpublishedConcerts->contains($concertC));
        $this->assertFalse($unpublishedConcerts->contains($anotherPromoterConcert));
        $this->assertTrue($unpublishedConcerts->contains($unpublishedConcertA));
        $this->assertTrue($unpublishedConcerts->contains($unpublishedConcertB));
        $this->assertTrue($unpublishedConcerts->contains($unpublishedConcertC));
        $this->assertFalse($unpublishedConcerts->contains($anotherPromoterUnpublishedConcert));
    }
}

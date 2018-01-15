<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessPosterImage;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake();
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__fixtures__/full-size-poster.png'))
        );

        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $resizedImage = Storage::disk('public')->get('posters/example-poster.png');
        [$width, $height] = getimagesizefromstring($resizedImage);

        $this->assertEquals(600, $width);
        $this->assertEquals(776, $height);
    }

    /** @test */
    function it_resizes_the_poster_image()
    {
        Storage::fake();
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__fixtures__/small-unoptimized-poster.png'))
        );

        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $originalSize = filesize(base_path('tests/__fixtures__/small-unoptimized-poster.png'));
        $optimizedImageSize = Storage::disk('public')->size('posters/example-poster.png');
        $this->assertLessThan($originalSize, $optimizedImageSize);

        $optimizedImageContents = Storage::disk('public')->get('posters/example-poster.png');
        $controlImageContents = file_get_contents(base_path('tests/__fixtures__/optimized-poster.png'));
        $this->assertEquals($optimizedImageContents, $controlImageContents);
    }
}

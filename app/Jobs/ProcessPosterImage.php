<?php

namespace App\Jobs;

use App\Concert;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Image;
use Storage;

class ProcessPosterImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Concert */
    public $concert;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $imageContents = Storage::disk('public')->get($this->concert->poster_image_path);
        $image = Image::make($imageContents);
        $image->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
        })->limitColors(255)->encode();
        Storage::disk('public')->put($this->concert->poster_image_path, (string)$image);
    }
}

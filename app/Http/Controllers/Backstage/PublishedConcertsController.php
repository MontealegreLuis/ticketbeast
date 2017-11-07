<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
 namespace App\Http\Controllers\Backstage;

use App\Concert;
use Auth;

class PublishedConcertsController
{
    public function store()
    {
        $promoter = Auth::user();

        /** @var Concert $concert */
        $concert = $promoter->concerts()->findOrFail(request('concert_id'));

        abort_if($concert->isPublished(), 422);

        $concert->publish();

        return redirect()->route('backstage.concerts.index');
    }
}

<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
 namespace App\Http\Controllers\Backstage;

use Auth;

class ConcertMessagesController
{
    public function create($id)
    {
        $promoter = Auth::user();
        $concert = $promoter->concerts()->findOrFail($id);
        return view('backstage.concert-messages.new', ['concert' => $concert]);
    }
}

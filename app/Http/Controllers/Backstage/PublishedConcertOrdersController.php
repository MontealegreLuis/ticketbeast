<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
 namespace App\Http\Controllers\Backstage;

use Auth;

class PublishedConcertOrdersController
{
    public function index($id)
    {
        $promoter = Auth::user();
        $concert = $promoter->concerts()->published()->findOrFail($id);
        return view('backstage.published-concerts.index', ['concert' => $concert]);
    }
}
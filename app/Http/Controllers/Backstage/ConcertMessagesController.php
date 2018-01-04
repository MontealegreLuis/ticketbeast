<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
 namespace App\Http\Controllers\Backstage;

use App\Http\Requests\MessageRequest;
use Auth;

class ConcertMessagesController
{
    public function create($id)
    {
        $promoter = Auth::user();
        $concert = $promoter->concerts()->findOrFail($id);
        return view('backstage.concert-messages.new', ['concert' => $concert]);
    }

    public function store($id, MessageRequest $request)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $message = $concert->attendeeMessages()->create(request(['subject', 'message']));

        return redirect()
            ->route('backstage.concert-messages.new', $concert)
            ->with('flash', 'Your message has been sent')
        ;
    }
}

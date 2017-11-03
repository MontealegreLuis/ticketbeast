<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Controllers\Backstage;

use App\Concert;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddConcertRequest;
use Auth;
use Carbon\Carbon;

class ConcertsController extends Controller
{
    public function index()
    {
        $promoter = Auth::user();
        return view('backstage.concerts.index', ['concerts' => $promoter->concerts]);
    }

    public function create()
    {
        return view('backstage/concerts/create');
    }

    public function store(AddConcertRequest $request)
    {
        $promoter = Auth::user();

        /** @var Concert $concert */
        $concert = $promoter->concerts()->create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(sprintf('%s %s', request('date'),  request('time'))),
            'ticket_price' => request('ticket_price') * 100,
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
        ]);
        $concert->addTickets(request('ticket_quantity'));
        $concert->publish();

        return redirect()->route('concerts.show', $concert);
    }
}

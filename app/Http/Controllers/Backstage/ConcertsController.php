<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Controllers\Backstage;

use App\Concert;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConcertRequest;
use Auth;
use Carbon\Carbon;

class ConcertsController extends Controller
{
    public function index()
    {
        $promoter = Auth::user();
        return view('backstage.concerts.index', [
            'publishedConcerts' => $promoter->concerts->filter->isPublished(),
            'unpublishedConcerts' => $promoter->concerts->reject->isPublished(),
        ]);
    }

    public function create()
    {
        return view('backstage/concerts/create');
    }

    public function store(ConcertRequest $request)
    {
        $promoter = Auth::user();

        $promoter->concerts()->create([
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
            'ticket_quantity' => request('ticket_quantity'),
            'poster_image_path' => request('poster_image')->store('posters', 's3'),
        ]);

        return redirect()->route('backstage.concerts.index');
    }

    public function edit($id)
    {
        $promoter = Auth::user();

        /** @var Concert $concert */
        $concert = $promoter->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', [
            'concert' => $concert
        ]);
    }

    public function update($id, ConcertRequest $request)
    {
        $promoter = Auth::user();

        /** @var Concert $concert */
        $concert = $promoter->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        $concert->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(sprintf('%s %s', request('date'),  request('time'))),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => request('ticket_quantity'),
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}

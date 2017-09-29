<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Concert::class)->states('published')->create([
            'title' => 'The red chord',
            'subtitle' => 'with Animosity and the Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17196',
            'additional_information' => 'For tickets call (555) 222-2222.',
        ])->addTickets(10);
    }
}

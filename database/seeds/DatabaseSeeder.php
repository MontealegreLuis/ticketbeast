<?php

use App\Billing\FakePaymentGateway;
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
        $faker = \Faker\Factory::create();
        $gateway = new FakePaymentGateway;
        $promoter = factory(App\User::class)->create([
            'email' => "luis@example.com",
            'password' => bcrypt('secret'),
        ]);

        $concert  = factory(Concert::class)->states('published')->create([
            'user_id' => $promoter->id,
            'title' => 'The red chord',
            'subtitle' => 'with Animosity and the Lethargy',
            'additional_information' => 'For tickets call (555) 222-2222.',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17196',
            'date' => Carbon::today()->addMonths(3)->hour(20),
            'ticket_price' => 3250,
            'ticket_quantity' => 250,
        ]);

        foreach(range(1, 50) as $i) {
            Carbon::setTestNow(Carbon::instance($faker->dateTimeBetween('-2 months')));
            $concert->reserveTickets(rand(1, 4), $faker->safeEmail)
                ->complete($gateway, $gateway->getValidTestToken($faker->creditCardNumber), 'test_acct_1234');
        }

        Carbon::setTestNow();

        factory(Concert::class)->create([
            'user_id' => $promoter->id,
            'title' => 'Slayer',
            'subtitle' => 'with Forbidden and Testament',
            'additional_information' => null,
            'venue' => 'The Rock Pile',
            'venue_address' => '55 Sample Blvd',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '19276',
            'date' => Carbon::today()->addMonths(6)->hour(19),
            'ticket_price' => 5500,
            'ticket_quantity' => 10,
        ]);
    }
}

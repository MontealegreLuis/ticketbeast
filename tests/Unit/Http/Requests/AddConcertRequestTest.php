<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Requests;

use Tests\TestCase;
use Validator;

class AddConcertRequestTest extends TestCase
{
    /** @test */
    function it_makes_title_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'title' => '',
        ]));

        $this->assertErrorFor($validator, 'title');
    }

    /** @test */
    function it_makes_subtitle_optional()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'subtitle' => '',
        ]));

        $this->assertFalse($validator->fails());
        $this->assertEquals(0, $validator->errors()->count());
    }

    /** @test */
    function it_makes_additional_information_optional()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'additional_information' => '',
        ]));

        $this->assertFalse($validator->fails());
        $this->assertEquals(0, $validator->errors()->count());
    }

    /** @test */
    function it_makes_date_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'date' => '',
        ]));

        $this->assertErrorFor($validator, 'date');
    }

    /** @test */
    function it_checks_date_is_valid()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'date' => 'not a date',
        ]));

        $this->assertErrorFor($validator, 'date');
    }

    /** @test */
    function it_makes_time_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'time' => '',
        ]));

        $this->assertErrorFor($validator, 'time');
    }

    /** @test */
    function it_checks_time_is_valid()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'time' => 'not a time',
        ]));

        $this->assertErrorFor($validator, 'time');
    }

    /** @test */
    function it_makes_venue_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'venue' => '',
        ]));

        $this->assertErrorFor($validator, 'venue');
    }

    /** @test */
    function it_makes_venue_address_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'venue_address' => '',
        ]));

        $this->assertErrorFor($validator, 'venue_address');
    }

    /** @test */
    function it_makes_city_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'city' => '',
        ]));

        $this->assertErrorFor($validator, 'city');
    }

    /** @test */
    function it_makes_state_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'state' => '',
        ]));

        $this->assertErrorFor($validator, 'state');
    }

    /** @test */
    function it_makes_zip_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'zip' => '',
        ]));

        $this->assertErrorFor($validator, 'zip');
    }

    /** @test */
    function it_makes_ticket_price_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_price' => '',
        ]));

        $this->assertErrorFor($validator, 'ticket_price');
    }

    /** @test */
    function it_checks_ticket_price_is_numeric()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_price' => 'not a number',
        ]));

        $this->assertErrorFor($validator, 'ticket_price');
    }

    /** @test */
    function it_checks_ticket_price_is_at_least_5_dollars()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_price' => '4.99',
        ]));

        $this->assertErrorFor($validator, 'ticket_price');
    }

    /** @test */
    function it_makes_ticket_quantity_required()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_quantity' => '',
        ]));

        $this->assertErrorFor($validator, 'ticket_quantity');
    }

    /** @test */
    function it_checks_ticket_quantity_is_numeric()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_quantity' => 'not a number',
        ]));

        $this->assertErrorFor($validator, 'ticket_quantity');
    }

    /** @test */
    function it_checks_ticket_quantity_is_at_least_1()
    {
        $validator = $this->validatorFor($this->concertInformation([
            'ticket_quantity' => '0',
        ]));

        $this->assertErrorFor($validator, 'ticket_quantity');
    }

    private function concertInformation(array $information = [])
    {
        return array_merge([
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => 'You must be 19 years of age to attend this concert.',
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ], $information);
    }

    private function validatorFor($input)
    {
        return Validator::make($input, (new ConcertRequest())->rules());
    }

    private function assertErrorFor($validator, $field)
    {
        $this->assertTrue($validator->fails());
        $this->assertEquals(1, $validator->errors()->count());
        $this->assertTrue($validator->errors()->has($field), "There are no errors for $field");
    }
}

<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit;

use App\RandomIdentifierGenerator;
use App\Ticket;
use Tests\TestCase;

class RandomIdentifierGeneratorTest extends TestCase
{
    /** @test */
    function it_generates_a_confirmation_number_24_characters_long()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    function it_generates_a_confirmation_number_with_uppercase_letters_and_digits()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }
    
    /** @test */
    function it_generates_a_confirmation_number_without_ambiguous_characters()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
    }

    /** @test */
    function it_generates_a_unique_confirmation_number()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $confirmationNumbers = collect(range(1, 100))->map(function () use ($generator) {
            return $generator->generateConfirmationNumber();
        });

        $this->assertCount(100, array_unique($confirmationNumbers->toArray()));
    }

    /** @test */
    function it_generates_a_ticket_code_at_least_6_characters_long()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $code = $generator->generateCodeFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    function it_generates_ticket_codes_with_only_uppercase_letters()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $code = $generator->generateCodeFor(new Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-Z]+$/', $code);
    }

    /** @test */
    function it_generates_the_same_code_for_the_same_ticket()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $code1 = $generator->generateCodeFor(new Ticket(['id' => 1]));
        $code2 = $generator->generateCodeFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    function it_generates_different_codes_for_different_tickets()
    {
        $generator = new RandomIdentifierGenerator('test-salt');

        $code1 = $generator->generateCodeFor(new Ticket(['id' => 1]));
        $code2 = $generator->generateCodeFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    function it_generates_different_codes_for_the_same_ticket_using_a_different_salt()
    {
        $generator1 = new RandomIdentifierGenerator('test-salt-1');
        $generator2 = new RandomIdentifierGenerator('test-salt-2');

        $code1 = $generator1->generateCodeFor(new Ticket(['id' => 1]));
        $code2 = $generator2->generateCodeFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}

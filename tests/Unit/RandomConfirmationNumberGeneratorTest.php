<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit;

use App\RandomIdentifierGenerator;
use Tests\TestCase;

class RandomConfirmationNumberGeneratorTest extends TestCase
{
    /** @test */
    function it_creates_a_confirmation_number_24_characters_long()
    {
        $generator = new RandomIdentifierGenerator();

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    function it_creates_a_confirmation_number_with_uppercase_letters_and_digits()
    {
        $generator = new RandomIdentifierGenerator();

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }
    
    /** @test */
    function it_creates_a_confirmation_number_without_ambigous_characters()
    {
        $generator = new RandomIdentifierGenerator();

        $confirmationNumber = $generator->generateConfirmationNumber();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
    }

    /** @test */
    function it_creates_a_unique_confirmation_number()
    {
        $generator = new RandomIdentifierGenerator();

        $confirmationNumbers = collect(range(1, 100))->map(function () use ($generator) {
            return $generator->generateConfirmationNumber();
        });

        $this->assertCount(100, array_unique($confirmationNumbers->toArray()));
    }
}

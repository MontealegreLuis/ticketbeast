<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit\Http\Requests;

use App\Http\Requests\PurchaseTicketsRequest;
use Tests\TestCase;
use Validator;

class PurchaseTicketsRequestTest extends TestCase
{
    /** @test */
    function it_makes_email_required()
    {
        $validator = $this->validatorFor([
            'ticket_quantity' => 3,
            'payment_token' => 'valid-token',
        ]);

        $this->assertErrorFor($validator, 'email');
    }

    /** @test */
    function it_requires_a_valid_email_address()
    {
        $validator = $this->validatorFor([
            'ticket_quantity' => 3,
            'payment_token' => 'valid-token',
            'email' => 'not-an-email',
        ]);

        $this->assertErrorFor($validator, 'email');
    }

    /** @test */
    function it_makes_quantity_required()
    {
        $validator = $this->validatorFor([
            'payment_token' => 'valid-token',
            'email' => 'jane@example.com',
        ]);

        $this->assertErrorFor($validator, 'ticket_quantity');
    }

    /** @test */
    function it_checks_quantity_is_greater_than_zero()
    {
        $validator = $this->validatorFor([
            'payment_token' => 'valid-token',
            'email' => 'jane@example.com',
            'ticket_quantity' => 0,
        ]);

        $this->assertErrorFor($validator, 'ticket_quantity');
    }

    /** @test */
    function it_makes_payment_token_required()
    {
        $validator = $this->validatorFor([
            'email' => 'jane@example.com',
            'ticket_quantity' => 1,
        ]);

        $this->assertErrorFor($validator, 'payment_token');
    }

    private function validatorFor($input)
    {
        return Validator::make($input, (new PurchaseTicketsRequest())->rules());
    }

    private function assertErrorFor($validator, $field)
    {
        $this->assertTrue($validator->fails());
        $this->assertEquals(1, $validator->errors()->count());
        $this->assertTrue($validator->errors()->has($field));
    }
}

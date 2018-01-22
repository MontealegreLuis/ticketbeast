<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Validator as RequestValidator;
use Tests\TestCase;
use Validator;

class InvitationRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_makes_email_required()
    {
        $validator = $this->validatorFor($this->invitationInformation([
            'email' => '',
        ]));

        $this->assertErrorFor($validator, 'email');
    }

    /** @test */
    function it_validates_email_contains_a_valid_email_address()
    {
        $validator = $this->validatorFor($this->invitationInformation([
            'email' => 'not-an-email-address',
        ]));

        $this->assertErrorFor($validator, 'email');
    }

    /** @test */
    function it_makes_sure_email_is_unique()
    {
        $existingPromoter = factory(User::class)->create(['email' => 'jane@example.com']);

        $validator = $this->validatorFor($this->invitationInformation([
            'email' => $existingPromoter->email,
        ]));

        $this->assertErrorFor($validator, 'email');
    }

    /** @test */
    function it_makes_password_required()
    {
        $validator = $this->validatorFor($this->invitationInformation([
            'password' => '',
        ]));

        $this->assertErrorFor($validator, 'password');
    }

    private function invitationInformation(array $information = [])
    {
        return array_merge([
            'email' => 'jane@example.com',
            'password' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ], $information);
    }

    private function validatorFor($input)
    {
        return Validator::make($input, (new InvitationRequest())->rules());
    }

    private function assertErrorFor(RequestValidator $validator, string $field)
    {
        $this->assertTrue($validator->fails());
        $this->assertEquals(1, $validator->errors()->count());
        $this->assertTrue($validator->errors()->has($field), "There are no errors for $field");
    }
}

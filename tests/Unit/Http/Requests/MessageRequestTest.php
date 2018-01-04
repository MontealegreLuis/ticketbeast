<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Requests;

use Tests\TestCase;
use Validator;

class MessageRequestTest extends TestCase 
{
    /** @test */
    function it_makes_subject_required()
    {
        $validator = $this->validatorFor([
            'subject' => '',
            'message' => 'Howdy!',
        ]);

        $this->assertErrorFor($validator, 'subject');
    }

    /** @test */
    function it_makes_message_required()
    {
        $validator = $this->validatorFor([
            'subject' => 'Howdy!',
            'message' => '',
        ]);

        $this->assertErrorFor($validator, 'message');
    }

    private function validatorFor($input)
    {
        return Validator::make($input, (new MessageRequest())->rules());
    }

    private function assertErrorFor($validator, $field)
    {
        $this->assertTrue($validator->fails());
        $this->assertEquals(1, $validator->errors()->count());
        $this->assertTrue($validator->errors()->has($field), "There are no errors for $field");
    }
}

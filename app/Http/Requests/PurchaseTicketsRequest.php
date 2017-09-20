<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseTicketsRequest extends FormRequest
{
    /** @return bool */
    public function authorize()
    {
        return true;
    }

    /**  @return array */
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required'],
        ];
    }
}

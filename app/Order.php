<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

use App\Billing\Charge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    protected $guarded = [];

    public static function forPurchase(
        Collection $tickets,
        string $email,
        Charge $charge,
        string $confirmationNumber
    ): Order
    {
        $order = self::create([
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four_digits' => $charge->cardLastFour(),
            'confirmation_number' => $confirmationNumber,
        ]);
        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public static function withConfirmationNumber(string $confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketsQuantity()
    {
        return $this->tickets()->count();
    }

    public function toArray()
    {
        return [
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'ticket_quantity' => $this->ticketsQuantity(),
            'amount' => $this->amount
        ];
    }
}

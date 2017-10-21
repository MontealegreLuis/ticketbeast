<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Integration;

use App\Billing\Charge;
use App\Order;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function it_is_created_from_purchase()
    {
        $tickets = factory(Ticket::class, 3)->create();
        $charge = new Charge(['amount' => 3600, 'card_last_four' => '1234']);

        $order = Order::forPurchase(
            $tickets,
            'jane@example.com',
            $charge,
            'order-number-123'
        );

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four_digits);
    }

    /** @test */
    function it_can_be_converted_to_array()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'confirmation_number_123',
            'email' => 'jane@example.com',
            'amount' => 6000,
            'card_last_four_digits' => '1234',
        ]);
        $order->tickets()->saveMany(factory(Ticket::class)->times(5)->create([
            'code' => '123',
        ]));

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
            'confirmation_number' => 'confirmation_number_123',
        ], $order->toArray());
    }

    /** @test */
    function it_can_be_found_by_its_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'order_confirmation_1234',
        ]);

        $foundOrder = Order::withConfirmationNumber('order_confirmation_1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    function it_fails_to_find_an_order_with_an_unknown_confirmation_number()
    {
        $this->expectException(ModelNotFoundException::class);
        Order::withConfirmationNumber('unknown_confirmation_number');
    }
}

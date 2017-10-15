<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function customer_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->create();
        $order = factory(Order::class)->create([
            'confirmation_number' => 'order_confirmation_1234',
            'card_last_four_digits' => '1881',
            'amount' => 8500,
        ]);
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'ticket_code_123',
        ]);
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'ticket_code_456',
        ]);

        $response = $this->get("/orders/order_confirmation_1234");

        $response->assertStatus(200);
        $response->assertViewHas('order', function (Order $viewOrder) use ($order) {
            return $viewOrder->id === $order-> id;
        });
        $response->assertSee('order_confirmation_1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('ticket_code_123');
        $response->assertSee('ticket_code_456');
    }
}

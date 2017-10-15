<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Controllers;

use App\Order;

class OrdersController extends Controller
{
    public function show(string $confirmationNumber)
    {
        $order = Order::withConfirmationNumber($confirmationNumber)->first();
        return view('orders.show', ['order' => $order]);
    }
}

<?php

namespace App\Events;

use App\Concert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConcertAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Concert */
    public $concert;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Concert $concert)
    {
        //
        $this->concert = $concert;
    }
}

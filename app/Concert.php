<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];
    protected $dates = ['date'];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    public function reserveTickets($quantity)
    {
        return $this->findTickets($quantity)->each(function ($ticket) {
            $ticket->reserve();
        });
    }

    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();
        if ($tickets->count() < $quantity) {
            throw NotEnoughTickets::available($tickets->count(), $quantity);
        }

        return $tickets;
    }

    public function createOrder($email, $tickets)
    {
        return Order::forPurchase($tickets, $email, $tickets->sum('price'));
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $_) {
            $this->tickets()->create();
        }
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function hasOrderFor($email)
    {
        return $this->orders()->where('email', $email)->count() > 0;
    }

    public function orderFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }
}

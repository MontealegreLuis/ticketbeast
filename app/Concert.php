<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Concert extends Model
{
    protected $guarded = [];
    protected $dates = ['date'];

    public function reserveTickets(int $quantity, string $email): Reservation
    {
        $tickets = $this->findTickets($quantity)->each(function (Ticket $ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    /**
     * @throws \App\NotEnoughTickets
     */
    public function findTickets(int $quantity): Collection
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();
        if ($tickets->count() < $quantity) {
            throw NotEnoughTickets::available($tickets->count(), $quantity);
        }

        return $tickets;
    }

    public function addTickets($quantity): void
    {
        foreach (range(1, $quantity) as $_) {
            $this->tickets()->create();
        }
    }

    public function ticketsRemaining(): int
    {
        return $this->tickets()->available()->count();
    }

    public function ticketsSold(): int
    {
        return $this->tickets()->sold()->count();
    }

    public function hasOrderFor($email): bool
    {
        return $this->orders()->where('email', $email)->count() > 0;
    }

    public function orderFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }

    public function hasPoster(): bool
    {
        return $this->poster_image_path !== null;
    }

    public function posterUrl(): string
    {
        return Storage::disk('public')->url($this->poster_image_path);
    }

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
        return Order::whereIn('id', $this->tickets()->pluck('order_id'));
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function attendeeMessages()
    {
        return $this->hasMany(AttendeeMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function publish(): void
    {
        $this->update(['published_at' => Carbon::now()]);
        $this->addTickets($this->ticket_quantity);
    }

    public function totalTickets(): int
    {
        return $this->tickets()->count();
    }

    public function percentSoldOut(): float
    {
        return round(($this->ticketsSold() / $this->totalTickets()) * 100, 2);
    }

    public function revenueInDollars(): float
    {
        return $this->orders()->sum('amount') / 100;
    }
}

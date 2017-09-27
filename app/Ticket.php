<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = [];

    public function release(): void
    {
        $this->update(['reserved_at' => null]);
    }

    public function reserve(): void
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendeeMessage extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->concert->orders();
    }

    public function withChunkedRecipients(int $chunkSize, callable $callback)
    {
        $this->orders()->chunk($chunkSize, function ($orders) use ($callback) {
            $callback($orders->pluck('email'));
        });
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }
}

<?php

namespace App;

use App\Mail\InvitationEmail;
use Illuminate\Database\Eloquent\Model;
use Mail;

class Invitation extends Model
{
    protected $guarded = [];

    public static function findByCode(string $code): Invitation
    {
        return self::where('code', $code)->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasBeenUsed(): bool
    {
        return $this->user_id !== null;
    }

    public function send(): void
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }
}

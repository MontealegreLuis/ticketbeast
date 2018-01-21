<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Invitation;
use App\User;
use Auth;
use Hash;

class RegisterController extends Controller
{
    public function register()
    {
        $invitation = Invitation::findByCode(request('invitation_code'));

        abort_if($invitation->hasBeenUsed(), 404);

        $promoter = User::create([
            'email' => request('email'),
            'password' => Hash::make(request('password')),
        ]);
        $invitation->update(['user_id' => $promoter->id]);

        Auth::login($promoter);

        return redirect()->route('backstage.concerts.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Invitation;

class InvitationsController extends Controller
{
    public function show(string $code)
    {
        $invitation = Invitation::findByCode($code);

        return view('invitations.show', [
            'invitation' => $invitation,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Auth;
use Zttp\Zttp;

class StripeConnectController extends Controller
{
    public function authorizeRedirect()
    {
        return redirect(sprintf(
            '%s?%s',
            'https://connect.stripe.com/oauth/authorize',
            http_build_query([
                'response_type' => 'code',
                'scope' => 'read_write',
                'client_id' => config('services.stripe.client_id')
            ])
        ));
    }

    public function redirect()
    {
        $accessTokenResponse = Zttp::asFormParams()->post('https://connect.stripe.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => request('code'),
            'client_secret' => config('services.stripe.secret'),
        ])->json();

        Auth::user()->update([
            'stripe_account_id' => $accessTokenResponse['stripe_user_id'],
            'stripe_access_token' => $accessTokenResponse['access_token'],
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}

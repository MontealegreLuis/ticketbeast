<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\ForceStripeAccount;

Route::get('/concerts/{id}', 'ConcertsController@show')->name('concerts.show');
Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');
Route::get('/orders/{confirmationNumber}', 'OrdersController@show');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/register', 'Auth\RegisterController@register')->name('auth.register');

Route::get('/invitations/{code}', 'InvitationsController@show')->name('invitations.show');

Route::group(['middleware' => 'auth', 'prefix' => 'backstage'], function () {
    Route::group(['middleware' => ForceStripeAccount::class], function () {
        Route::get('/concerts', 'Backstage\ConcertsController@index')->name('backstage.concerts.index');
        Route::get('/concerts/new', 'Backstage\ConcertsController@create')->name('backstage.concerts.new');
        Route::post('/concerts', 'Backstage\ConcertsController@store')->name('backstage.concerts.store');
        Route::get('/concerts/{id}/edit', 'Backstage\ConcertsController@edit')->name('backstage.concerts.edit');
        Route::patch('/concerts/{id}', 'Backstage\ConcertsController@update')->name('backstage.concerts.update');

        Route::post('/published-concerts', 'Backstage\PublishedConcertsController@store')->name('backstage.published-concerts.store');
        Route::get('/published-concerts/{id}/orders', 'Backstage\PublishedConcertOrdersController@index')->name('backstage.published-concerts.index');

        Route::get('/concerts/{id}/messages/new', 'Backstage\ConcertMessagesController@create')->name('backstage.concert-messages.new');
        Route::post('/concerts/{id}/messages', 'Backstage\ConcertMessagesController@store')->name('backstage.concert-messages.store');
    });

    Route::get('/stripe-connect/connect', 'Backstage\StripeConnectController@connect')->name('backstage.stripe-connect.connect');
    Route::get('/stripe-connect/authorize', 'Backstage\StripeConnectController@authorizeRedirect')->name('backstage.stripe-connect.authorize');
    Route::get('/stripe-connect/redirect', 'Backstage\StripeConnectController@redirect')->name('backstage.stripe-connect.redirect');
});

<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\IdentifierGenerator;
use App\RandomIdentifierGenerator;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind(StripePaymentGateway::class, function() {
            return new StripePaymentGateway(config("services.stripe.secret"));
        });
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        $this->app->bind(
            IdentifierGenerator::class,
            RandomIdentifierGenerator::class
        );
    }
}

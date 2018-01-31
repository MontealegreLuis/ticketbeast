<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\ForceStripeAccount;
use App\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ForceStripeAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_are_force_to_connect_with_stripe()
    {
        $promoter = factory(User::class)->create([
            'stripe_account_id' => null,
        ]);
        $this->be($promoter);

        $middleware = new ForceStripeAccount();

        $response = $middleware->handle(new Request(), function ($request) {
            $this->fail('It should not have called next middleware');
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('backstage.stripe-connect.connect'), $response->getTargetUrl());
    }

    /** @test */
    function users_with_stripe_account_can_continue()
    {
        $promoter = factory(User::class)->create([
            'stripe_account_id' => 'STRIPE_ACCOUNT_1234',
        ]);
        $this->be($promoter);
        $next = new class {
            public $wasCalledWithRequest = null;
            public function __invoke($request)
            {
                $this->wasCalledWithRequest = $request;
            }
        };
        $middleware = new ForceStripeAccount();
        $request = new Request();

        $middleware->handle($request, Closure::fromCallable($next));

        $this->assertSame($request, $next->wasCalledWithRequest);
    }
}

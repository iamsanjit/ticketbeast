<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Illuminate\Support\Facades\Schema;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\TicketCodeGenerator;
use App\HashidsTicketCodeGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Older version of mysql only support 767 bytes of key length
        // We will force laravel to use keylenght with in that range
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }

        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(HashidsTicketCodeGenerator::class, function () {
            return new HashidsTicketCodeGenerator(config('app.ticket_code_salt'));
        });

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        $this->app->bind(TicketCodeGenerator::class, HashidsTicketCodeGenerator::class);
    }
}

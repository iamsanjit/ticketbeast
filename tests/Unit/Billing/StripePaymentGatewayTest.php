<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\StripePaymentGateway;
use App\Billing\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/** @group integration */
class StripePaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    private function lastCharge()
    {
        return array_first(\Stripe\Charge::all(["limit" => 1], ['api_key' => config('services.stripe.secret')])['data']);
    }

    private function newCharges()
    {
        return \Stripe\Charge::all(
            [
                "limit" => 1,
                'ending_before' => $this->lastCharge
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }

    private function validToken()
    {
        return \Stripe\Token::create([
            "card" => [
                "number" => "4242424242424242",
                "exp_month" => 9,
                "exp_year" => 2019,
                "cvc" => "314"
            ]
        ], ['api_key' => config('services.stripe.secret')])->id;
    }

    /** @test */
    public function payments_with_valid_test_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        $lastCharge = $this->lastCharge();

        $paymentGateway->charge(2500, $this->validToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }

    /** @test */
    public function charges_with_invalid_token_fail()
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        $lastCharge = $this->lastCharge();

        try {
            $paymentGateway->charge(2500, 'invalid-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail('Charges with invalid payment token did not throw an exception.');
    }
}

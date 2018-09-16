<?php

namespace App\Billing;

use Stripe\Error\InvalidRequest;

class StripePaymentGateway implements PaymentGateway
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge(int $amount, String $token) : void
    {
        try {
            \Stripe\Charge::create([
                "amount" => $amount,
                "currency" => "cad",
                "source" => $token,
            ], ['api_key' => $this->apiKey]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException();
        }
    }

    public function getValidTestToken()
    {
        return \Stripe\Token::create([
            "card" => [
                "number" => "4242424242424242",
                "exp_month" => 9,
                "exp_year" => 2019,
                "cvc" => "314"
            ]
        ], ['api_key' => $this->apiKey])->id;
    }

    private function lastCharge()
    {
        return array_first(\Stripe\Charge::all(["limit" => 1], ['api_key' => config('services.stripe.secret')])['data']);
    }

    private function newChargesSince($charge = null)
    {
        $newCharges = \Stripe\Charge::all([
            'ending_before' => $charge !== null ? $charge->id : null,
        ], ['api_key' => config('services.stripe.secret')])['data'];
        return collect($newCharges)->pluck('amount');
    }

    public function newChargesDuring($callback)
    {
        $lastCharge = $this->lastCharge();
        $callback($this);
        return $this->newChargesSince($lastCharge);
    }
}

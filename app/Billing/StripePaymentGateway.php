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
}
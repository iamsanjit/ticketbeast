<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'test-token';   
    }

    public function charge(int $amount, String $token) : void
    {
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }
        $this->charges->push($amount);
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

}
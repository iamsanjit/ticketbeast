<?php

namespace App\Billing;

interface PaymentGateway
{
    public function charge(int $amount, String $token) : void;

    public function getValidTestToken();
    public function newChargesDuring($callback);
}
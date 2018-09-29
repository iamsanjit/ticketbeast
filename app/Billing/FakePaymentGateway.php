<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    const DEFAULT_TEST_CARD = '0000000000001881';
    private $charges;
    private $tokens;
    private $beforeFirstCharge;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstCharge = $callback;
    }

    public function getValidTestToken($card = self::DEFAULT_TEST_CARD)
    {
        $token = 'fake-tok_'.str_random(24);
        $this->tokens[$token] = $card;
        return $token;
    }

    public function charge(int $amount, String $token)
    {
        if ($this->beforeFirstCharge !== null) {
            $callback = $this->beforeFirstCharge;
            $this->beforeFirstCharge = null;
            $callback($this);
        }

        if (!$this->tokens->has($token)) {
            throw new PaymentFailedException;
        }
        
        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4)
        ]);
    }

    public function newChargesDuring($callback)
    {
        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }
}

<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;


trait PaymentGatewayContractTest
{

    abstract protected function getPaymentGateway();

    /** @test */
    public function payments_with_valid_test_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2100, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2100, $newCharges->sum());
    }

    /** @test */
    public function can_fetch_charges_created_during_callback()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });

        $this->assertEquals([5000, 4000], $newCharges->all());
    }

    /** @test */
    public function charges_with_invalid_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            try {
                $paymentGateway->charge(2500, 'invalid-payment-token');
            } catch (PaymentFailedException $e) {
                return;
            }
            $this->fail('Charges with invalid payment token did not throw an exception.');
        });

        $this->assertEquals(0, $paymentGateway->totalCharges());
    }

}
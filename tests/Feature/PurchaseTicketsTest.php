<?php 

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;



class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_purchase_a_ticket()
    {
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 3265
        ]);

        $response = $this->withoutExceptionHandling()->json('POST', '/concerts/'.$concert->id.'/orders', [
            'email' => 'jane@example.com',
            'quantity' => '2',
            'token' => $paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);
        
        // Assert we charge customer correct amount
        $this->assertEquals(6530, $paymentGateway->totalCharges());
        
        // Assert that an order exist for the customer
        $order = $concert->orders()->whereEmail('jane@example.com')->first();
        $this->assertNotNull($order);

        // Assert that order has correct amount of tickets
        $this->assertEquals(2, $order->tickets()->count());
    }
}
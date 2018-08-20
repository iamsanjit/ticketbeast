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

    private $paymentGateway;

    protected function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        return $this->json('POST', '/concerts/'.$concert->id.'/orders', $params);
    }

    private function assertResponseHasError($key, $response)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($key, $response->decodeResponseJson()['errors'], 'Failed asserting that response contains "'.$key.'" error.');
    }

    /** @test */
    public function user_can_purchase_a_ticket()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 3265
        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => '2',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);
        
        // Assert we charge customer correct amount
        $this->assertEquals(6530, $this->paymentGateway->totalCharges());
        
        // Assert that an order exist for the customer
        $order = $concert->orders()->whereEmail('jane@example.com')->first();
        $this->assertNotNull($order);

        // Assert that order has correct amount of tickets
        $this->assertEquals(2, $order->tickets()->count());
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'quantity' => '2',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseHasError('email', $response);
    }

    /** @test */
    public function email_must_be_valid_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'invalid-email-address',
            'quantity' => '2',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseHasError('email', $response);
    }

    /** @test */
    public function quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseHasError('quantity', $response);
    }

    /** @test */
    public function quantity_must_be_an_integer_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 'not-an-integer',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseHasError('quantity', $response);
    }

    /** @test */
    public function quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 0,
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseHasError('quantity', $response);
    }

    /** @test */
    public function token_is_required_to_purchase_tickets()
    {
        // $this->withoutExceptionHandling();
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 2,
        ]);

        $this->assertResponseHasError('token', $response);
    }
}

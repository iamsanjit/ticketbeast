<?php 

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Request;


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
        $savedRequest = $this->app['request'];
        $response = $this->json('POST', '/concerts/'.$concert->id.'/orders', $params);
        $this->app['request'] = $savedRequest;
        return $response;
    }

    private function assertResponseHasError($key, $response)
    {
        $response->assertStatus(422);
        $this->assertArrayHasKey($key, $response->decodeResponseJson()['errors'], 'Failed asserting that response contains "'.$key.'" error.');
    }

    /** @test */
    public function user_can_purchase_tickets_to_published_concert()
    {
        $this->withoutExceptionHandling();
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 3265])->addTickets(5);

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => '2',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);
        $response->assertExactJson([
            'email' => 'jane@example.com',
            'ticket_quantity' => 2,
            'amount' => 6530
        ]);
        $this->assertEquals(6530, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('jane@example.com'));
        $this->assertEquals(2, $concert->orderFor('jane@example.com')->ticketQuantity());
    }

    /** @test */
    public function user_cannot_purchase_ticket_to_unpublished_concert()
    {
        $concert = factory(Concert::class)->state('unpublished')->create()->addTickets(5);

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => '2',
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(404);
        $this->assertEquals(5, $concert->tickets()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function cannot_purchase_tickets_other_customer_already_trying_to_purchase()
    {
        $this->withoutExceptionHandling();
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 1200])->addTickets(3);

        $this->paymentGateway->beforeFirstCharge(function($paymentGateway) use ($concert){
            $response = $this->orderTickets($concert, [
                'email' => 'personB@example.com',
                'quantity' => '2',
                'payment_token' => $this->paymentGateway->getValidTestToken()
            ]);
            $response->assertStatus(422);
            $this->assertFalse($concert->hasOrderFor('personB@example.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $response = $this->orderTickets($concert, [
            'email' => 'personA@example.com',
            'quantity' => '2',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);
        $this->assertEquals(1, $concert->ticketsRemaining());
        $this->assertTrue($concert->hasOrderFor('personA@example.com'));
        $this->assertEquals(2400, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(5);

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
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 2,
        ]);

        $this->assertResponseHasError('payment_token', $response);
    }
    
    /** @test */
    public function order_is_not_created_if_payment_is_unsuccessful()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(2);

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 2,
            'token' => 'invalid-token'
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('jane@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannot_purchase_tickets_more_than_remaining()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(50);

        $response = $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'quantity' => 51,
            'token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('jane@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }
}

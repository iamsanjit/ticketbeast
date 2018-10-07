<?php 

namespace Tests\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Ticket;
use App\Billing\Charge;
use Mockery;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_an_order_from_tickets_email_and_amount()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);
        $charge = new Charge(['amount' => 3600, 'card_last_four' => '1234']);
        $order = Order::forTickets($tickets, 'jane@example.com', $charge);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals('1234', $order->card_last_four);
        $this->assertEquals(3600, $order->amount);
        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }
    /** @test */
    public function converting_to_an_array()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION123',
            'email' => 'jane@example.com',
            'amount' => 10000,
        ]);
        $order->tickets()->saveMany([
            factory(Ticket::class)->create(['code' => 'TICKETCODE1']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE2']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE3']),
        ]);

        $this->assertArraySubset([
            'confirmation_number' => 'ORDERCONFIRMATION123',
            'email' => 'jane@example.com',
            'amount' => 10000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ]
        ], $order->toArray());
    }

    /** @test */
    public function retreving_an_order_by_confirmation_number()
    {
        $order = factory(Order::class)->create(['confirmation_number' => 'ORDERCONFIMATION123']);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIMATION123');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    public function retreving_a_nonexisting_order_by_confirmation_number_throws_an_exception()
    {
        $order = null;

        try {
            $order = Order::findByConfirmationNumber('ORDERCONFIMATION123');
        } catch (ModelNotFoundException $e) {
            $this->assertNull($order);
            return;
        }

        $this->fail('No matching order was found for the given confirmation number, but the exception was not thrown');
    }
}

<?php 

namespace Test\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Ticket;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_an_order_from_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(5);
        $tickets = $concert->findTickets(3);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($tickets, 'jane@example.com', $tickets->sum('price'));

        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(2, $concert->ticketsRemaining());
    }
    /** @test */
    public function converting_to_an_array()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION123',
            'email' => 'jane@example.com',
            'amount' => 10000,
        ]);
        $order->tickets()->saveMany(factory(Ticket::class, 5)->create());

        $this->assertArraySubset([
            'confirmation_number' => 'ORDERCONFIRMATION123',
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 10000,
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
        try {
            Order::findByConfirmationNumber('ORDERCONFIMATION123');
        } catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matching order was found for the given confirmation number, but the exception was not thrown');
    }
}

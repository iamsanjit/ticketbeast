<?php 

namespace Test\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Order;

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
        $concert = factory(Concert::class)->state('published')->create(['ticket_price' => 2000])->addTickets(5);
        $order = $concert->orderTickets(5, 'jane@example.com');

        $this->assertArraySubset([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 10000,
        ], $order->toArray());
    }
}
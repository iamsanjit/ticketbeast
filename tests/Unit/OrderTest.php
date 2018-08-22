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
    /** @test */
    public function tickets_are_released_when_order_is_created()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(10);
        $order = $concert->orderTickets(5, 'jane@example.com');
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}
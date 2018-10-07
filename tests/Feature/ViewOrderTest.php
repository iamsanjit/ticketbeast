<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Order;
use App\Ticket;
use App\Concert;
use Carbon\Carbon;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        // Create a concert
        $concert = factory(Concert::class)->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Ethargy',
            'date' => Carbon::parse('December 13, 2019 8:00pm')
        ]);

        // Create an order
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION12345',
            'amount' => 8500,
            'card_last_four' => 1881
        ]);
        // Create tickets
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE123'
        ]);
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE456'
        ]);

        // Get order confimation page
        $response = $this->get('orders/ORDERCONFIRMATION12345');
        
        $response->assertStatus(200);
        
        // View the order data
        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id == $order->id;
        });

        $response->assertSee('ORDERCONFIRMATION12345');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKETCODE123');
        $response->assertSee('TICKETCODE456');
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Ethargy');
        $response->assertSee('2019-12-13 20:00');
    }
}

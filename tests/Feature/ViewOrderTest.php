<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Order;
use App\Ticket;
use App\Concert;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        // Create a concert
        $concert = factory(Concert::class)->create();
        // Create an order
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION12345'
        ]);
        // Create tickets
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);

        // Get order confimation page
        $response = $this->get('orders/ORDERCONFIRMATION12345');
        
        $response->assertStatus(200);
        
        // View the order data
        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $viewOrder->id == $order->id;
        });
    }
}

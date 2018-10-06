<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Ticket;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use App\Order;
use App\Facades\TicketCode;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function ticket_attribute_returns_the_concerts_ticket_price()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 2000]);
        $ticket = factory(Ticket::class)->create(['concert_id' => $concert->id]);

        $this->assertEquals(2000, $ticket->price);
    }

    /** @test */
    public function ticket_can_be_reserved()
    {
        $ticket = factory(Ticket::class)->create();
        
        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function ticket_can_be_released()
    {
        $ticket = factory(Ticket::class)->state('reserved')->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function a_ticket_can_be_claimed_for_an_order()
    {
        $order = factory(Order::class)->create();
        $ticket = factory(Ticket::class)->create(['code' => null]);
        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');

        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->fresh()->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}

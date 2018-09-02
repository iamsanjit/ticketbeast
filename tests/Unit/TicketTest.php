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
        $ticket = factory(Ticket::class)->create(['reserved_at' => Carbon::now()]);
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function cannot_reserved_a_ticket_that_have_already_been_purchased()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->orderTickets(2, 'jane@example.com');
        $this->assertEquals(1, $concert->ticketsRemaining());

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving ticket succeed even though tickets were already sold.');
    }

    /** @test */
    public function cannot_reserved_a_ticket_that_have_already_been_reserved()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->reserveTickets(2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving ticket succeed even though tickets were already reserved.');
    }

}

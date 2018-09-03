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
        $ticket = factory(Ticket::class)->state('reserved')->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

}

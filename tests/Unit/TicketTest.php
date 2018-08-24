<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Ticket;

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

}

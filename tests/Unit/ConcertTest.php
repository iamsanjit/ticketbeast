<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Exceptions\NotEnoughTicketsException;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make(['date' => Carbon::parse('2017-12-01 8:00pm')]);
        $this->assertEquals('December 1, 2017', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make(['date' => Carbon::parse('2017-12-01 20:00')]);
        $this->assertEquals('8:00pm', $concert->formatted_start_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollors()
    {
        $concert = factory(Concert::class)->make(['ticket_price' => 6750]);
        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concert_with_published_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->states('published')->create();
        $publishedConcertB = factory(Concert::class)->states('published')->create();
        $unpublishedConcert = factory(Concert::class)->states('unpublished')->create();

        $publishConcerts = Concert::published()->get();

        $this->assertTrue($publishConcerts->contains($publishedConcertA));
        $this->assertTrue($publishConcerts->contains($publishedConcertB));
        $this->assertFalse($publishConcerts->contains($unpublishedConcert));
    }

    /** @test */
    public function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(5);

        $order = $concert->orderTickets(2, 'jane@example.com');

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(2, $order->tickets()->count());
    }

    /** @test */
    public function can_add_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(50);
        $this->assertEquals(50, $concert->tickets()->count());
    }

    /** @test */
    public function ticket_remaining_does_not_include_tickets_associate_with_orders()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(50);
        $concert->orderTickets(30, 'jane@example.com');
        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    public function trying_to_purchase_more_tickets_than_remaining_throws_an_exception()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(10);

        try {
            $concert->orderTickets(30, 'jane@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(10, $concert->ticketsRemaining());
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            return;
        }
        
        $this->fail('Order succeed even though not enought tickets was available');
    }

    /** @test */
    public function can_reserve_available_tickets()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(10);
        $this->assertEquals(10, $concert->ticketsRemaining());
        
        $reservation = $concert->reserveTickets(3);

        $this->assertCount(3, $reservation->tickets());
        $this->assertEquals(7, $concert->ticketsRemaining());
    }

    /** @test */
    public function can_not_order_tickets_that_is_already_been_purchased()
    {
        $concert = factory(Concert::class)->state('published')->create()->addTickets(10);
        try {
            $concert->orderTickets(8, 'jane@example.com');
            $concert->orderTickets(3, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeed even though not enought tickets was available');
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

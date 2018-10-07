<?php 

namespace Tests\Unit;

use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Reservation;
use Mockery;
use App\Ticket;
use App\Billing\FakePaymentGateway;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1000],
            (object) ['price' => 1000],
            (object) ['price' => 1000]
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');

        $this->assertEquals(3000, $reservation->totalCharges());
    }

    /** @test */
    public function reserved_tickets_are_released_when_the_reservation_is_canceled()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);
        $reservation = new Reservation($tickets, 'jane@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }

    /** @test */
    public function retreving_the_reservation_tickets()
    {
        $tickets = collect([
            (object)['price' => 1000],
            (object)['price' => 1000],
            (object)['price' => 1000]
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');

        $this->assertCount(3, $reservation->tickets());
    }

    /** @test */
    public function completing_the_reservation_returns_order()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'jane@example.com');
        $paymentGateway = new FakePaymentGateway;

        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }

    /** @test */
    public function retreving_the_reservation_email()
    {
        $reservation = new Reservation(collect(), 'jane@example.com');

        $this->assertEquals('jane@example.com', $reservation->email());
    }
}

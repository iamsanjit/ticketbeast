<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Concert;
use Carbon\Carbon;

class AddConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function promoters_can_view_add_concert_form()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_cannot_view_add_concert_form()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function promoter_can_add_concert()
    {
        $this->withoutExceptionHandling();
        
        $user = factory(User::class)->create();
        
        $response = $this->actingAs($user)->post('/backstage/concerts', [
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Ethargy',
            'date' => '2019-10-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
            'additional_information' => 'This concert is 19+',
        ]);

        tap(Concert::first(), function ($concert) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect('/concerts/1');

            $this->assertEquals('The Red Chord', $concert->title);
            $this->assertEquals('with Animosity and Ethargy', $concert->subtitle);
            $this->assertEquals(Carbon::parse('2019-10-18 8:00pm'), $concert->date);
            $this->assertEquals('The Mosh Pit', $concert->venue);
            $this->assertEquals('123 Example Lane', $concert->venue_address);
            $this->assertEquals('Laraville', $concert->city);
            $this->assertEquals('ON', $concert->state);
            $this->assertEquals('17916', $concert->zip);
            $this->assertEquals('3250', $concert->ticket_price);
            $this->assertEquals('This concert is 19+', $concert->additional_information);
            $this->assertEquals(75, $concert->ticketsRemaining());
        });
    }

    /** @test */
    public function guest_cannot_add_concert()
    {
        $response = $this->post('/backstage/concerts', [
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Ethargy',
            'date' => '2019-10-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
            'additional_information' => 'This concert is 19+',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertCount(0, Concert::all());
    }
}

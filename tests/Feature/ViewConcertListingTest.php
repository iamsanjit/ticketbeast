<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_view_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Ethargy',
            'date' => Carbon::parse('December 13, 2017 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets call 555-555-5555',
        ]);

        $response = $this->withoutExceptionHandling()->get('/concerts/'.$concert->id);

        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Ethargy');
        $response->assertSee('December 13, 2017');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('For tickets call 555-555-5555');
    }

    /** @test */
    public function user_cannot_view_unpublished_concert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->get('/concerts/' . $concert->id);
        
        $response->assertStatus(404);
    }
}

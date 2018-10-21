<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Concert;
use Carbon\Carbon;

class AddConcertTest extends TestCase
{
    use DatabaseMigrations;

    public function validParams($overrides = [])
    {
        return array_merge([
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
        ], $overrides);
    }

    public function from($url)
    {
        session()->setPreviousUrl(url($url));
        return $this;
    }

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

        tap(Concert::first(), function ($concert) use ($response, $user) {
            $response->assertStatus(302);
            $response->assertRedirect('backstage/concerts');

            $this->assertTrue($concert->user->is($user));

            $this->assertFalse($concert->isPublished());

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
            $this->assertEquals(0, $concert->ticketsRemaining());
        });
    }

    /** @test */
    public function guest_cannot_add_concert()
    {
        $response = $this->post('/backstage/concerts', $this->validParams());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function title_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'title' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['title']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/concerts', [
            'title' => 'The Red Chord',
            'subtitle' => '',
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
            $this->assertNull($concert->subtitle);
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
    public function additional_information_is_optional()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/concerts', [
            'title' => 'The Red Chord',
            'subtitle' => 'with anmosity',
            'date' => '2019-10-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
            'additional_information' => '',
        ]);

        tap(Concert::first(), function ($concert) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect('/concerts/1');

            $this->assertEquals('The Red Chord', $concert->title);
            $this->assertEquals('with anmosity', $concert->subtitle);
            $this->assertEquals(Carbon::parse('2019-10-18 8:00pm'), $concert->date);
            $this->assertEquals('The Mosh Pit', $concert->venue);
            $this->assertEquals('123 Example Lane', $concert->venue_address);
            $this->assertEquals('Laraville', $concert->city);
            $this->assertEquals('ON', $concert->state);
            $this->assertEquals('17916', $concert->zip);
            $this->assertEquals('3250', $concert->ticket_price);
            $this->assertNull($concert->additional_information);
            $this->assertEquals(75, $concert->ticketsRemaining());
        });
    }

    /** @test */
    public function date_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'date' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['date']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function date_must_be_valid_date()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'date' => 'invalid-date',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['date']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function time_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'time' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['time']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function time_must_be_a_valid_time()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'time' => 'invalid-time',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['time']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function venue_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'venue' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['venue']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function venue_address_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'venue_address' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['venue_address']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function city_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'city' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['city']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function state_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'state' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['state']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function zip_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'zip' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['zip']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_price_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_price' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_price']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_price_must_be_numeric()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_price' => 'non numeric price',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_price']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_price_must_be_at_least_5_dollors()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_price' => '4.99',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_price']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_quantity_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_quantity' => '',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_quantity']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_quantity_must_be_integer()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_quantity' => '1.3',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_quantity']);
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->from('/backstage/concerts/create')->post('/backstage/concerts', $this->validParams([
            'ticket_quantity' => '0',
        ]));

        $response->assertRedirect('/backstage/concerts/create');
        $response->assertSessionHasErrors(['ticket_quantity']);
        $this->assertEquals(0, Concert::count());
    }
}

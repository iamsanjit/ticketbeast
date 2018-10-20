<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Concert;
use Carbon\Carbon;

class EditConcertTest extends TestCase
{
    use DatabaseMigrations;

    private function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2019-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New venue address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '32.50',
            'ticket_quantity' => '10',
        ], $overrides);
    }

    /** @test */
    public function promoter_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $user->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->get('/backstage/concerts/'.$concert->id.'/edit');
        
        $response->assertStatus(200);
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    public function promoter_can_not_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->state('published')->create(['user_id' => $user->id]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->get('/backstage/concerts/' . $concert->id . '/edit');

        $response->assertStatus(403);
    }

    /** @test */
    public function promoter_cannot_view_the_edit_form_for_others_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts/' . $concert->id . '/edit');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function promoter_see_the_error_404_when_attempting_to_edit_the_concert_that_does_not_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/999/edit');

        $response->assertStatus(404);
    }

    /** @test */
    public function guests_are_asked_for_login_when_attempting_to_view_the_edit_form_for_any_concerts()
    {
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        $response = $this->get('/backstage/concerts/' . $concert->id . '/edit');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function guests_are_asked_for_login_when_attempting_to_view_the_edit_form_for_concert_that_does_not_exist()
    {
        $response = $this->get('/backstage/concerts/999/edit');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function promoter_can_update_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->patch('/backstage/concerts/'.$concert->id, [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2019-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New venue address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '32.50',
            'ticket_quantity' => '10',
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('New title', $concert->title);
            $this->assertEquals('New subtitle', $concert->subtitle);
            $this->assertEquals('New additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2019-12-12 8:00pm'), $concert->date);
            $this->assertEquals('New venue', $concert->venue);
            $this->assertEquals('New venue address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals(3250, $concert->ticket_price);
            $this->assertEquals(10, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function promoter_cannot_update_others_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $concert = factory(Concert::class)->create([
            'user_id' => $otherUser->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->patch('/backstage/concerts/' . $concert->id, [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2019-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New venue address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '32.50',
            'ticket_quantity' => '10',
        ]);

        $response->assertStatus(404);

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old venue address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function promoter_cannot_update_published_concerts()
    {
        // $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->state('published')->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'ticket_quantity' => 10,
        ]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->patch('/backstage/concerts/' . $concert->id, [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2019-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New venue address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '32.50',
            'ticket_quantity' => '5',
        ]);

        $response->assertStatus(403);

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old venue address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
            $this->assertEquals(10, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function guests_cannot_update_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->state('unpublished')->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->patch('/backstage/concerts/' . $concert->id, [
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2019-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New venue address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '32.50',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old venue address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    public function title_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/'.$concert->id, $this->validParams([
            'title' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['title']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
        });
    }

    /** @test */
    public function subtitle_is_optional()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'ticket_quantity' => 10,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'subtitle' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('New title', $concert->title);
            $this->assertNull($concert->subtitle);
            $this->assertEquals('New additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2019-12-12 8:00pm'), $concert->date);
            $this->assertEquals('New venue', $concert->venue);
            $this->assertEquals('New venue address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals(3250, $concert->ticket_price);
        });
    }

    /** @test */
    public function additional_information_is_optional()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'additional_information' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('New title', $concert->title);
            $this->assertEquals('New subtitle', $concert->subtitle);
            $this->assertNull($concert->additional_information);
            $this->assertEquals(Carbon::parse('2019-12-12 8:00pm'), $concert->date);
            $this->assertEquals('New venue', $concert->venue);
            $this->assertEquals('New venue address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals(3250, $concert->ticket_price);
        });
    }

    /** @test */
    public function date_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2017-01-01 5:00pm'),
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'date' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['date']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
        });
    }

    /** @test */
    public function date_must_be_a_valid_date()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2017-01-01 5:00pm'),
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'date' => 'invalid-date',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['date']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
        });
    }

    /** @test */
    public function time_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2017-01-01 5:00pm'),
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'time' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['time']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
        });
    }

    /** @test */
    public function time_must_be_a_valid_time()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => Carbon::parse('2017-01-01 5:00pm'),
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'time' => 'invalid-time',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['time']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
        });
    }

    /** @test */
    public function venue_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'venue' => 'Old venue',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'venue' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['venue']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old venue', $concert->venue);
        });
    }

    /** @test */
    public function venue_address_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'venue_address' => 'Old venue address',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'venue_address' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['venue_address']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old venue address', $concert->venue_address);
        });
    }

    /** @test */
    public function city_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'city' => 'Old city',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'city' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['city']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old city', $concert->city);
        });
    }

    /** @test */
    public function state_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'state' => 'Old state',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'state' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['state']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old state', $concert->state);
        });
    }

    /** @test */
    public function zip_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'zip' => '00000',
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'zip' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['zip']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('00000', $concert->zip);
        });
    }

    /** @test */
    public function ticket_price_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 2000,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_price' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_price']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_price_must_be_numeric()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 2000,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_price' => 'non-numeric',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_price']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_price_must_be_at_least_5_dollors()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 2000,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_price' => '4',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_price']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    public function ticket_quantity_is_required()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_quantity' => '',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_quantity']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function ticket_quantity_must_be_integer()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_quantity' => '9.5',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_quantity']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());


        $response = $this->actingAs($user)->from("/backstage/concerts/{$concert->id}/edit")->patch('/backstage/concerts/' . $concert->id, $this->validParams([
            'ticket_quantity' => '0',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors(['ticket_quantity']);
        tap($concert->fresh(), function ($concert) {
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }
}

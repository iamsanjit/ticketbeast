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
            'additional_information' => 'Old additinal information',
            'venue' => 'Old venue',
            'venue_address' => 'Old venue address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
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
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function($concert) {
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
        });
    }
}
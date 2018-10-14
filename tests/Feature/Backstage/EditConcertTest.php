<?php 

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Concert;

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
}
<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class PublishedConcertsTests extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function promoter_can_publish_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = ConcertFactory::createUnpublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertRedirect('/backstage/concerts');
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function promoter_cannot_publish_others_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = ConcertFactory::createUnpublished([
            'user_id' => $otherUser->id,
            'ticket_quantity' => 3,
        ]);
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertStatus(404);
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    public function guest_cannot_publish_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createUnpublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertRedirect('/login');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    public function promoter_cannot_publish_concert_twice()
    {
        $user = factory(User::class)->create();
        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertStatus(422);
        $concert = $concert->fresh();
        $this->assertEquals(3, $concert->ticketsRemaining());
    }
}

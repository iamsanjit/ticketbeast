<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Concert;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Assert;


class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {
        parent::setUp();

        TestResponse::macro('getData', function($key) {
            return $this->original->getData()[$key];
        });

        Collection::macro('assertContains', function($data) {
            Assert::assertTrue($this->contains($data));
        });

        Collection::macro('assertNotContains', function($data) {
            Assert::assertFalse($this->contains($data));
        });
    }

    /** @test */
    public function guest_cannot_see_concert_listing()
    {
        $concerts = factory(Concert::class, 3)->create();

        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function promoter_can_only_view_a_list_of_their_own_concerts()
    {
        $this->withoutExceptionHandling();
        
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $concertA = factory(Concert::class)->create(['user_id' => $userA->id]);
        $concertB = factory(Concert::class)->create(['user_id' => $userA->id]);
        $concertC = factory(Concert::class)->create(['user_id' => $userB->id]);
        $concertD = factory(Concert::class)->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userA)->get('/backstage/concerts');

        $response->assertStatus(200);
        $response->getData('concerts')->assertContains($concertA);
        $response->getData('concerts')->assertContains($concertB);
        $response->getData('concerts')->assertContains($concertD);
        $response->getData('concerts')->assertNotContains($concertC);
    }
}
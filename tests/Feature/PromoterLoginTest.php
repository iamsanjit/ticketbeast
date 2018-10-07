<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use Illuminate\Support\Facades\Auth;

class PromoterLoginTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function login_with_valid_credentials()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function login_with_invalid_credentials()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'invalid-password'
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function login_with_credentials_that_does_not_exist()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'invalid-password'
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }
}

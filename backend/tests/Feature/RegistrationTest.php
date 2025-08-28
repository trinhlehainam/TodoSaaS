<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_endpoint_exists(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is not enabled.');
        }

        $response = $this->postJson('/register', []);

        // Should get validation errors, not 404
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_new_users_can_register(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is not enabled.');
        }

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201);
        $this->assertAuthenticated();
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_users_cannot_register_with_existing_email(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is not enabled.');
        }

        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_users_cannot_register_with_mismatched_passwords(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is not enabled.');
        }

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_users_cannot_register_with_weak_password(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration is not enabled.');
        }

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * Test suite for email verification functionality.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that email can be verified with valid signed URL.
     */
    public function test_email_can_be_verified(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification is not enabled.');
        }

        Event::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect();
    }

    /**
     * Test that email is not verified with invalid hash.
     */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification is not enabled.');
        }

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test that verification email can be resent for unverified users.
     */
    public function test_email_verification_can_be_resent(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification is not enabled.');
        }

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->postJson('/email/verification-notification');

        $response->assertStatus(202);
    }

    /**
     * Test that verified users cannot resend verification email.
     */
    public function test_verified_user_cannot_resend_verification_email(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification is not enabled.');
        }

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson('/email/verification-notification');

        $response->assertStatus(204);
    }
}
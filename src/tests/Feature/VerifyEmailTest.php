<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_redirect_button_navigates_to_verification_page()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
        $response->assertSee('http://localhost:8025/');
    }

    public function test_verified_user_is_redirected_to_item_list_page()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertNotNull($user->fresh()->email_verified_at);

        $response->assertRedirect('/attendance?verified=1');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_pages_are_accessible(): void
    {
        $this->get('/forgot-password')->assertStatus(200);

        $user = User::factory()->create();
        $this->get('/reset-password/fake-token?email='.$user->email)->assertStatus(200);
    }

    public function test_password_reset_link_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email])->assertStatus(302);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}

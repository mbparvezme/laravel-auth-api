<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

beforeEach(fn () => Notification::fake());

it('sends password reset link', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/password/forgot', ['email' => $user->email])
        ->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('returns success even for unknown email to prevent enumeration', function () {
    $this->postJson('/api/v1/password/forgot', ['email' => 'nobody@example.com'])
        ->assertStatus(200);
});

it('resets password with valid token', function () {
    $user  = User::factory()->create();
    $token = Password::createToken($user);

    $this->postJson('/api/v1/password/reset', [
        'token'                 => $token,
        'email'                 => $user->email,
        'password'              => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ])->assertStatus(200);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'NewPassword123!',
    ])->assertStatus(200);
});

it('rejects invalid reset token', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/password/reset', [
        'token'                 => 'invalid-token',
        'email'                 => $user->email,
        'password'              => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ])->assertStatus(422);
});

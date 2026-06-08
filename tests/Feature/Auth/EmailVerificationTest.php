<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('verifies email with valid signed url', function () {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
        'id'   => $user->id,
        'hash' => sha1($user->email),
    ]);

    $path = parse_url($url, PHP_URL_PATH) . '?' . parse_url($url, PHP_URL_QUERY);

    $this->getJson($path)->assertStatus(200);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects invalid signature', function () {
    $user = User::factory()->unverified()->create();

    $this->getJson("/api/v1/verify-email/{$user->id}/badhash?expires=9999999999&signature=bad")
        ->assertStatus(403);
});

it('returns success for already verified email', function () {
    $user = User::factory()->create();

    $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
        'id'   => $user->id,
        'hash' => sha1($user->email),
    ]);

    $path = parse_url($url, PHP_URL_PATH) . '?' . parse_url($url, PHP_URL_QUERY);

    $this->getJson($path)->assertStatus(200)
        ->assertJsonFragment(['success' => true]);
});

it('resends verification email', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->postJson('/api/v1/resend-verification')
        ->assertStatus(200);

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

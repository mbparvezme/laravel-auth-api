<?php

use App\Features\MagicLink\Models\MagicLink;
use App\Features\MagicLink\Notifications\MagicLinkNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

// --- Request endpoint ---

it('sends magic link to a registered email', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/magic-link/request', ['email' => $user->email])
        ->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    Notification::assertSentTo($user, MagicLinkNotification::class);
    $this->assertDatabaseHas('magic_links', ['email' => $user->email]);
});

it('returns 200 for unregistered email to prevent enumeration', function () {
    $this->postJson('/api/v1/magic-link/request', ['email' => 'nobody@example.com'])
        ->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    Notification::assertNothingSent();
});

it('does not send magic link to banned user', function () {
    $user = User::factory()->create(['status' => 'banned']);

    $this->postJson('/api/v1/magic-link/request', ['email' => $user->email])
        ->assertStatus(200);

    Notification::assertNothingSent();
    $this->assertDatabaseMissing('magic_links', ['email' => $user->email]);
});

it('does not send magic link to suspended user', function () {
    $user = User::factory()->create(['status' => 'suspended']);

    $this->postJson('/api/v1/magic-link/request', ['email' => $user->email])
        ->assertStatus(200);

    Notification::assertNothingSent();
});

it('requires a valid email to request magic link', function () {
    $this->postJson('/api/v1/magic-link/request', ['email' => 'not-an-email'])
        ->assertStatus(422);
});

// --- Verify endpoint ---

it('verifies a valid magic link and returns a token', function () {
    $user = User::factory()->create();
    [$link, $rawToken] = MagicLink::generate($user->email);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => $rawToken,
        'email' => $user->email,
    ])->assertStatus(200)
      ->assertJsonStructure(['data' => ['token', 'user']]);

    $this->assertDatabaseHas('magic_links', ['id' => $link->id]);
    expect($link->fresh()->used_at)->not->toBeNull();
});

it('rejects an expired magic link', function () {
    $user = User::factory()->create();
    [$link, $rawToken] = MagicLink::generate($user->email);
    $link->update(['expires_at' => now()->subMinutes(20)]);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => $rawToken,
        'email' => $user->email,
    ])->assertStatus(422);
});

it('rejects an already-used magic link', function () {
    $user = User::factory()->create();
    [$link, $rawToken] = MagicLink::generate($user->email);
    $link->update(['used_at' => now()]);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => $rawToken,
        'email' => $user->email,
    ])->assertStatus(422);
});

it('rejects a wrong token', function () {
    $user = User::factory()->create();
    MagicLink::generate($user->email);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => 'completely-wrong-token',
        'email' => $user->email,
    ])->assertStatus(422);
});

it('rejects when email does not match token', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();
    [, $rawToken] = MagicLink::generate($user->email);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => $rawToken,
        'email' => $other->email,
    ])->assertStatus(422);
});

it('rejects magic link for banned user at verify time', function () {
    $user = User::factory()->create();
    [, $rawToken] = MagicLink::generate($user->email);
    $user->update(['status' => 'banned']);

    $this->postJson('/api/v1/magic-link/verify', [
        'token' => $rawToken,
        'email' => $user->email,
    ])->assertStatus(403);
});

it('requires token and email to verify', function () {
    $this->postJson('/api/v1/magic-link/verify', [])->assertStatus(422);
});

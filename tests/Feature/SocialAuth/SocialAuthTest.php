<?php

use App\Features\SocialAuth\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function makeSocialUser(string $id, string $email, string $name = 'Social User'): SocialiteUser
{
    $user              = new SocialiteUser;
    $user->id          = $id;
    $user->email       = $email;
    $user->name        = $name;
    $user->token       = 'access-token';
    $user->refreshToken = null;
    $user->expiresIn   = 3600;
    return $user;
}

function mockProvider(string $provider, SocialiteUser $socialUser): void
{
    $mock = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
    $mock->shouldReceive('stateless')->andReturnSelf();
    $mock->shouldReceive('user')->andReturn($socialUser);
    Socialite::shouldReceive('driver')->with($provider)->andReturn($mock);
}

it('logs in user with existing social account', function () {
    $user    = User::factory()->create();
    $socUser = makeSocialUser('google-123', $user->email);
    mockProvider('google', $socUser);

    SocialAccount::create([
        'user_id'     => $user->id,
        'provider'    => 'google',
        'provider_id' => 'google-123',
    ]);

    $this->getJson('/api/v1/auth/google/callback')
        ->assertStatus(200)
        ->assertJsonStructure(['success', 'data' => ['token', 'user']]);
});

it('links social account to existing verified user by email', function () {
    $user    = User::factory()->create(['email' => 'linked@example.com']);
    $socUser = makeSocialUser('google-456', 'linked@example.com');
    mockProvider('google', $socUser);

    $this->getJson('/api/v1/auth/google/callback')->assertStatus(200);

    $this->assertDatabaseHas('social_accounts', [
        'user_id'     => $user->id,
        'provider'    => 'google',
        'provider_id' => 'google-456',
    ]);
});

it('rejects social login when email matches unverified account', function () {
    User::factory()->unverified()->create(['email' => 'unverified@example.com']);
    $socUser = makeSocialUser('google-789', 'unverified@example.com');
    mockProvider('google', $socUser);

    $this->getJson('/api/v1/auth/google/callback')->assertStatus(403);
});

it('registers new user via social auth', function () {
    $socUser = makeSocialUser('google-999', 'brand-new@example.com', 'New Person');
    mockProvider('google', $socUser);

    $this->getJson('/api/v1/auth/google/callback')
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['token', 'user']]);

    $this->assertDatabaseHas('users', ['email' => 'brand-new@example.com']);
    $this->assertDatabaseHas('social_accounts', ['provider_id' => 'google-999']);
});

it('rejects invalid provider on callback', function () {
    $this->getJson('/api/v1/auth/twitter/callback')->assertStatus(422);
});

it('rejects invalid provider on redirect', function () {
    $this->getJson('/api/v1/auth/twitter/redirect')->assertStatus(422);
});

<?php

use App\Features\MultiDevice\Models\DeviceSession;
use App\Models\User;

it('lists active sessions with device info', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('auth')->plainTextToken;

    // Hit a route so TrackDeviceSession middleware fires
    $this->withToken($token)->getJson('/api/v1/devices')->assertStatus(200);

    // Second call sees the device session recorded by the first call
    $response = $this->withToken($token)->getJson('/api/v1/devices')->assertStatus(200);

    $sessions = $response->json('data');
    expect($sessions)->toHaveCount(1);
    expect($sessions[0]['is_current'])->toBeTrue();
});

it('records device info via middleware on authenticated requests', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('auth')->plainTextToken;

    $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)')
        ->withToken($token)
        ->getJson('/api/v1/profile');

    $sanctumToken = $user->tokens()->first();
    $session = DeviceSession::where('token_id', $sanctumToken->id)->first();

    expect($session)->not->toBeNull();
    expect($session->platform)->toBe('Windows');
    expect($session->device)->toBe('Desktop');
});

it('revokes a specific session', function () {
    $user   = User::factory()->create();
    $token1 = $user->createToken('device-1')->plainTextToken;
    $token2 = $user->createToken('device-2')->plainTextToken;

    $tokenId = $user->tokens()->where('name', 'device-1')->value('id');

    $this->withToken($token2)->deleteJson("/api/v1/devices/{$tokenId}")->assertStatus(200);

    $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
});

it('returns 404 when revoking a session that does not belong to user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownerToken = $owner->createToken('auth')->plainTextToken;
    $ownerTokenId = $owner->tokens()->value('id');

    $otherToken = $other->createToken('auth')->plainTextToken;

    $this->withToken($otherToken)->deleteJson("/api/v1/devices/{$ownerTokenId}")->assertStatus(404);
});

it('revokes all other sessions while keeping current', function () {
    $user = User::factory()->create();
    $user->createToken('old-device-1');
    $user->createToken('old-device-2');
    $current = $user->createToken('current')->plainTextToken;

    $this->withToken($current)->deleteJson('/api/v1/devices/logout-others')->assertStatus(200);

    expect($user->tokens()->count())->toBe(1);
    expect($user->tokens()->first()->name)->toBe('current');
});

it('rejects unauthenticated device list request', function () {
    $this->getJson('/api/v1/devices')->assertStatus(401);
});

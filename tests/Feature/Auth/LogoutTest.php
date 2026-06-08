<?php

use App\Models\User;

it('logs out current device', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('device')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/logout')
        ->assertStatus(200)
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('logs out all devices', function () {
    $user = User::factory()->create();
    $user->createToken('device-1');
    $user->createToken('device-2');

    $this->actingAs($user)->postJson('/api/v1/logout-all')
        ->assertStatus(200);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('rejects unauthenticated logout', function () {
    $this->postJson('/api/v1/logout')->assertStatus(401);
});

<?php

use App\Models\AppLog;
use App\Models\User;

it('returns only user-visible logs for authenticated user', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    AppLog::create(['user_id' => $user->id,  'action' => 'LOGIN_OK',  'user_visible' => true]);
    AppLog::create(['user_id' => $user->id,  'action' => 'ERR_LOGIN', 'user_visible' => false]);
    AppLog::create(['user_id' => $other->id, 'action' => 'LOGIN_OK',  'user_visible' => true]);

    $response = $this->actingAs($user)->getJson('/api/v1/logs');

    $response->assertStatus(200);
    $data = $response->json('data.data');
    expect($data)->toHaveCount(1);
    expect($data[0]['action'])->toBe('LOGIN_OK');
});

it('rejects unauthenticated log access', function () {
    $this->getJson('/api/v1/logs')->assertStatus(401);
});

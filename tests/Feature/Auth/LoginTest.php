<?php

use App\Enums\UserStatus;
use App\Models\User;

it('logs in with valid credentials', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ])->assertStatus(200)
      ->assertJsonStructure(['success', 'message', 'data' => ['user', 'token']]);
});

it('rejects wrong password', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'WrongPassword!',
    ])->assertStatus(401);
});

it('rejects non-existent email', function () {
    $this->postJson('/api/v1/login', [
        'email'    => 'nobody@example.com',
        'password' => 'Password123!',
    ])->assertStatus(401);
});

it('rejects banned user', function () {
    $user = User::factory()->create([
        'password' => 'Password123!',
        'status'   => UserStatus::Banned,
    ]);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ])->assertStatus(403);
});

it('rejects suspended user', function () {
    $user = User::factory()->create([
        'password' => 'Password123!',
        'status'   => UserStatus::Suspended,
    ]);

    $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'Password123!',
    ])->assertStatus(403);
});

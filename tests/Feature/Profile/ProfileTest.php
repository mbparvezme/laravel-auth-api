<?php

use App\Models\User;

it('returns user profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/api/v1/profile')
        ->assertStatus(200)
        ->assertJsonStructure(['success', 'message', 'data' => ['id', 'name', 'email', 'status']]);
});

it('updates profile fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patchJson('/api/v1/profile', [
        'mobile' => '+8801712345678',
        'gender' => 'male',
        'bio'    => 'Hello world',
    ])->assertStatus(200);

    $this->assertDatabaseHas('profiles', ['user_id' => $user->id, 'mobile' => '+8801712345678']);
});

it('rejects profile access for unauthenticated user', function () {
    $this->getJson('/api/v1/profile')->assertStatus(401);
});

it('rejects profile access for unverified user', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->getJson('/api/v1/profile')->assertStatus(403);
});

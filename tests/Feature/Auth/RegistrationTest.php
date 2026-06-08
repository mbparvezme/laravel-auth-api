<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

it('registers a new user and sends verification email', function () {
    $response = $this->postJson('/api/v1/register', [
        'name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['success', 'message', 'data' => ['user' => ['id', 'name', 'email'], 'token']]);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    $this->assertDatabaseHas('profiles', ['user_id' => User::first()->id]);
    Notification::assertSentTo(User::first(), VerifyEmailNotification::class);
});

it('rejects registration with duplicate email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $this->postJson('/api/v1/register', [
        'name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertStatus(422);
});

it('rejects registration with weak password', function () {
    $this->postJson('/api/v1/register', [
        'name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => '123',
        'password_confirmation' => '123',
    ])->assertStatus(422);
});

it('rejects registration with mismatched password confirmation', function () {
    $this->postJson('/api/v1/register', [
        'name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Different123!',
    ])->assertStatus(422);
});

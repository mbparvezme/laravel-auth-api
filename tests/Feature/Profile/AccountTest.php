<?php

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\VerifyNewEmailNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => Notification::fake());

it('sends verification to new email without updating it immediately', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->actingAs($user)->patchJson('/api/v1/account/email', [
        'email'    => 'new@example.com',
        'password' => 'Password123!',
    ])->assertStatus(200);

    Notification::assertSentTo($user, VerifyNewEmailNotification::class);
    $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
});

it('rejects email update with wrong password', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->actingAs($user)->patchJson('/api/v1/account/email', [
        'email'    => 'new@example.com',
        'password' => 'WrongPassword!',
    ])->assertStatus(403);
});

it('updates password with valid current password', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->actingAs($user)->postJson('/api/v1/account/password', [
        'current_password'      => 'Password123!',
        'password'              => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ])->assertStatus(200);
});

it('rejects password update with wrong current password', function () {
    $user = User::factory()->create(['password' => 'Password123!']);

    $this->actingAs($user)->postJson('/api/v1/account/password', [
        'current_password'      => 'WrongPassword!',
        'password'              => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ])->assertStatus(403);
});

it('allows user to deactivate their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patchJson('/api/v1/account/status/inactive')
        ->assertStatus(200);

    expect($user->fresh()->status)->toBe(UserStatus::Inactive);
});

it('rejects invalid status value', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patchJson('/api/v1/account/status/banned')
        ->assertStatus(422);
});

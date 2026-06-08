<?php

use App\Features\IpRules\Models\IpRule;
use App\Models\User;

// --- Middleware: blocklist mode ---

it('blocks request from a blocklisted IP', function () {
    IpRule::create(['ip_address' => '1.2.3.4', 'type' => 'block']);

    $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
        ->getJson('/api/v1/profile')
        ->assertStatus(403)
        ->assertJsonFragment(['success' => false]);
});

it('allows request from a non-blocked IP in blocklist mode', function () {
    IpRule::create(['ip_address' => '1.2.3.4', 'type' => 'block']);
    $user = User::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])
        ->actingAs($user)
        ->getJson('/api/v1/profile')
        ->assertStatus(200);
});

it('blocks CIDR range in blocklist mode', function () {
    IpRule::create(['ip_address' => '10.0.0.0/24', 'type' => 'block']);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.50'])
        ->getJson('/api/v1/profile')
        ->assertStatus(403);
});

it('allows IP outside blocked CIDR range', function () {
    IpRule::create(['ip_address' => '10.0.0.0/24', 'type' => 'block']);
    $user = User::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
        ->actingAs($user)
        ->getJson('/api/v1/profile')
        ->assertStatus(200);
});

// --- Middleware: allowlist mode ---

it('allows request from an allowlisted IP', function () {
    IpRule::create(['ip_address' => '9.9.9.9', 'type' => 'allow']);
    $user = User::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
        ->actingAs($user)
        ->getJson('/api/v1/profile')
        ->assertStatus(200);
});

it('blocks request from IP not on allowlist when allowlist exists', function () {
    IpRule::create(['ip_address' => '9.9.9.9', 'type' => 'allow']);

    $this->withServerVariables(['REMOTE_ADDR' => '1.1.1.1'])
        ->getJson('/api/v1/profile')
        ->assertStatus(403);
});

it('passes all IPs when no rules exist', function () {
    $user = User::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])
        ->actingAs($user)
        ->getJson('/api/v1/profile')
        ->assertStatus(200);
});

// --- Management endpoints ---

it('lists ip rules for authenticated user', function () {
    $user = User::factory()->create();
    IpRule::create(['ip_address' => '1.2.3.4', 'type' => 'block', 'label' => 'Spammer']);

    $this->actingAs($user)->getJson('/api/v1/ip-rules')
        ->assertStatus(200)
        ->assertJsonStructure(['data' => [['id', 'ip_address', 'type', 'label']]]);
});

it('adds an ip rule', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/ip-rules', [
        'ip_address' => '192.168.1.100',
        'type'       => 'block',
        'label'      => 'Test block',
    ])->assertStatus(201);

    $this->assertDatabaseHas('ip_rules', ['ip_address' => '192.168.1.100', 'type' => 'block']);
});

it('adds a cidr rule', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/ip-rules', [
        'ip_address' => '192.168.0.0/16',
        'type'       => 'allow',
    ])->assertStatus(201);

    $this->assertDatabaseHas('ip_rules', ['ip_address' => '192.168.0.0/16']);
});

it('rejects invalid ip address', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/ip-rules', [
        'ip_address' => 'not-an-ip',
        'type'       => 'block',
    ])->assertStatus(422);
});

it('rejects invalid cidr prefix', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/ip-rules', [
        'ip_address' => '192.168.1.0/33',
        'type'       => 'block',
    ])->assertStatus(422);
});

it('removes an ip rule', function () {
    $user = User::factory()->create();
    $rule = IpRule::create(['ip_address' => '5.5.5.5', 'type' => 'block']);

    $this->actingAs($user)->deleteJson("/api/v1/ip-rules/{$rule->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('ip_rules', ['id' => $rule->id]);
});

it('returns 404 when removing non-existent ip rule', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->deleteJson('/api/v1/ip-rules/9999')
        ->assertStatus(404);
});

it('requires auth to manage ip rules', function () {
    $this->getJson('/api/v1/ip-rules')->assertStatus(401);
    $this->postJson('/api/v1/ip-rules', [])->assertStatus(401);
});

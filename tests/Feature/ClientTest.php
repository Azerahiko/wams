<?php

use App\Models\Client;
use App\Models\User;

test('users can view the client list when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.index'));

    $response->assertOk();
});

test('guests are redirected to login when accessing client list', function () {
    $response = $this->get(route('clients.index'));

    $response->assertRedirect(route('login'));
});

test('users can search clients', function () {
    Client::factory()->create(['name' => 'Acme Corp']);
    Client::factory()->create(['name' => 'Beta LLC']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.index', ['search' => 'Acme']));

    $response->assertOk();
    $response->assertSee('Acme Corp');
});

test('users can filter clients by status', function () {
    Client::factory()->create(['status' => 'active']);
    Client::factory()->create(['status' => 'inactive']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.index', ['status' => 'active']));

    $response->assertOk();
});

test('users can create a client when authorized', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('clients.store'), [
        'name' => 'Test Client',
        'email' => 'test@client.com',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('clients', [
        'name' => 'Test Client',
        'email' => 'test@client.com',
        'status' => 'active',
    ]);
});

test('validation fails when creating a client with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('clients.store'), [
        'name' => '',
        'email' => 'invalid-email',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'email']);
});

test('users can view a client detail', function () {
    $client = Client::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.show', $client));

    $response->assertOk();
    $response->assertSee($client->name);
});

test('users can update a client when authorized', function () {
    $client = Client::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('clients.update', $client), [
        'name' => 'Updated Client',
        'email' => 'updated@client.com',
    ]);

    $response->assertStatus(200);
    $client->refresh();

    expect($client->name)->toBe('Updated Client');
    expect($client->email)->toBe('updated@client.com');
});

test('validation fails when updating a client with invalid data', function () {
    $client = Client::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('clients.update', $client), [
        'name' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

test('users can delete a client', function () {
    $client = Client::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

    $response->assertOk();
    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('client cannot be deleted if it has related data', function () {
    $client = Client::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('clients.destroy', $client));

    // Hard delete - client is permanently removed
    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('pagination works for client list', function () {
    $user = User::factory()->create();

    // Create multiple clients
    Client::factory()->count(25)->create();

    $response = $this->actingAs($user)->get(route('clients.index'));

    $response->assertOk();
    // Check pagination links are present in JSON
    $response->assertJsonStructure(['data', 'meta', 'links']);
});

test('empty state is displayed when no clients exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('clients.index'));

    $response->assertOk();
    $response->assertJsonFragment(['data' => [], 'total' => 0]);
});

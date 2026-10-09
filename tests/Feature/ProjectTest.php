<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    Client::factory()->create();
});

test('users can view the project list when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.index'));

    $response->assertOk();
});

test('guests are redirected to login when accessing project list', function () {
    $response = $this->get(route('projects.index'));

    $response->assertRedirect(route('login'));
});

test('users can search projects', function () {
    Project::factory()->create(['name' => 'Acme Website']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.index', ['search' => 'Acme']));

    $response->assertOk();
    $response->assertSee('Acme Website');
});

test('users can filter projects by status', function () {
    Project::factory()->create(['status' => 'in_progress']);
    Project::factory()->create(['status' => 'completed']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.index', ['status' => 'in_progress']));

    $response->assertOk();
});

test('users can filter projects by client', function () {
    $client = Client::factory()->create();
    Project::factory()->create(['client_id' => $client->id, 'name' => 'Client Project']);
    Project::factory()->create(['name' => 'Orphan Project']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.index', ['client_id' => $client->id]));

    $response->assertOk();
    $response->assertSee('Client Project');
});

test('users can create a project when authorized', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'client_id' => $client->id,
        'name' => 'New Project',
        'description' => 'A new project description',
        'status' => 'planning',
        'budget' => 10000,
        'start_date' => '2024-01-01',
        'due_date' => '2024-12-31',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
        'client_id' => $client->id,
        'status' => 'planning',
    ]);
});

test('validation fails when creating a project with invalid client', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'client_id' => 999,
        'name' => 'New Project',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['client_id']);
});

test('validation fails when creating a project with invalid data', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'client_id' => $client->id,
        'name' => '',
        'status' => 'invalid_status',
        'budget' => -100,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'status', 'budget']);
});

test('validation fails when due_date precedes start_date', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'client_id' => $client->id,
        'name' => 'New Project',
        'status' => 'planning',
        'start_date' => '2024-06-01',
        'due_date' => '2024-01-01',
    ]);

    $response->assertStatus(422);
    $response->assertSee('Due date must not be before start date.');
});

test('users can view a project detail', function () {
    $project = Project::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertSee($project->name);
});

test('users can update a project when authorized', function () {
    $project = Project::factory()->create();

    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)->patch(route('projects.update', $project), [
        'name' => 'Updated Project',
        'status' => 'in_progress',
        'budget' => 15000,
    ]);

    $response->assertStatus(200);
    $project->refresh();

    expect($project->name)->toBe('Updated Project');
    expect($project->status)->toBe('in_progress');
    expect($project->budget)->toBe(15000);
});

test('validation fails when updating a project with invalid data', function () {
    $project = Project::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user = User::factory()->create())->patch(route('projects.update', $project), [
        'name' => '',
        'status' => 'invalid',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'status']);
});

test('users can delete a project', function () {
    $project = Project::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertOk();
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('project client association is valid', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertJson(['data' => ['client_id' => $client->id]]);
    $this->assertDatabaseHas('projects', ['id' => $project->id, 'client_id' => $client->id]);
});

test('pagination works for project list', function () {
    $user = User::factory()->create();

    // Create multiple projects
    Project::factory()->count(25)->create();

    $response = $this->actingAs($user)->get(route('projects.index'));

    $response->assertOk();
    $response->assertJsonStructure(['data', 'total']);
});

test('empty state is displayed when no projects exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('projects.index'));

    $response->assertOk();
    $response->assertJsonFragment(['data' => [], 'total' => 0]);
});

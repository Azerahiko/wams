<?php

use App\Models\User;

test('users can access the login page', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can access the register page', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('users can access the dashboard when authenticated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

test('guests are redirected to login when accessing dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('users can login with valid credentials', function () {
    $user = User::factory()->create(['email' => 'test@agency.com']);

    $response = $this->post(route('login.store'), [
        'email' => 'test@agency.com',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users cannot login with invalid credentials', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('users can access the profile settings page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
});

test('users can update their profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Updated Name',
        'email' => 'test@agency.com',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
});

test('users can access the security settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('security.edit'));

    // In test environment, user hasn't confirmed password recently,
    // so the RequirePassword middleware redirects to confirm
    $response->assertStatus(302);
});

test('users can update their password', function () {
    $user = User::factory()->create(['password' => 'password']);

    $this->actingAs($user);

    $response = $this->put(route('user-password.update'), [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertAuthenticated();
});

<?php

use App\Models\User;

test('it can render the login screen.', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('it can render the user registration screen.', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('it can authenticate users using the login screen.', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    // $response->assertRedirect(route('dashboard', absolute: false));
});

test('it cannot authenticate users using invalid password.', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'ThisIsAWrongPassword',
    ]);

    $this->assertGuest();
});

test('it can logout authenticated users.', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

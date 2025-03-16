<?php

test('it can render the registration screen.', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('it can register a user successfully.', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post('/register', $userData);

    $response->assertRedirect('/tasks'); // Redirect after registration
    $this->assertAuthenticated(); // Check user is logged in
    $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
});

test('registration fails if required fields are missing', function () {
    $response = $this->postJson('/register', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('registration fails if password confirmation does not match', function () {
    $response = $this->postJson('/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'wrongpassword',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['password']);
});

test('registration fails if email format is not valid', function () {
    $response = $this->postJson('/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@', // Invalid email format
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['email']);
});

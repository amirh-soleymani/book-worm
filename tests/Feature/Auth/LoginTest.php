<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in a user successfully', function () {
    // Arrange
    $password = 'password123';

    $user = User::factory()->create([
        'email' => 'amir@example.com',
        'password' => Hash::make($password),
    ]);

    // Act
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    // Assert
    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'You are logged in successfully.',
            'data' => [
                'token_type' => 'Bearer',
            ],
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
            ],
        ]);

    expect($user->tokens()->count())->toBe(1);
});

it('returns unauthorized response when credentials are invalid', function () {
    // Arrange
    User::factory()->create([
        'email' => 'amir@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    // Act
    $response = $this->postJson('/api/login', [
        'email' => 'amir@example.com',
        'password' => 'wrong-password',
    ]);

    // Assert
    $response
        ->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'message' => 'Invalid credentials',
        ]);
});

it('validates required fields', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/login', $payload);

    // Assert
    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'email',
            'password',
        ]);
});

it('validates email format', function () {
    // Arrange
    $payload = [
        'email' => 'invalid-email',
        'password' => 'password123',
    ];

    // Act
    $response = $this->postJson('/api/login', $payload);

    // Assert
    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'email',
        ]);
});

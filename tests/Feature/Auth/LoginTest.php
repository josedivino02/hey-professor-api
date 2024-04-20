<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertAuthenticatedAs, postJson};

it("Should be able to login", function () {
    $user = User::factory()->create(['email' => 'divino@divino.com', 'password' => Hash::make('password')]);

    postJson(route('login'), [
        "email"    => "divino@divino.com",
        "password" => "password",
    ])->assertNoContent();

    assertAuthenticatedAs($user);
});

it("should check if the email and password is valid", function ($email, $password) {
    $user = User::factory()->create(['email' => 'divino@divino.com', 'password' => Hash::make('password')]);

    postJson(route('login'), [
        "email"    => $email,
        "password" => $password,
    ])->assertJsonValidationErrors([
        'email' => __('auth.failed'),
    ]);

})->with([
    'wrong email'    => ['wrong@email.com', 'password'],
    'wrong password' => ['wrong@email.com', 'password123213'],
    'invalid email'  => ['invalid-email', 'password123213'],
]);

test('required fields', function () {
    postJson(route('login'), [
        "email"    => '',
        "password" => '',
    ])->assertJsonValidationErrors([
        'email'    => __('validation.required', ['attribute' => 'email']),
        'password' => __('validation.required', ['attribute' => 'password']),
    ]);
});

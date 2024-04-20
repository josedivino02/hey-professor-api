<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertAuthenticatedAs, assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

it("Should be able to register in the application", function () {
    postJson(route('register'), [
        "name"               => "Divino",
        "email"              => "divino@divino.com",
        "email_confirmation" => "divino@divino.com",
        "password"           => "password",
    ])->assertOk();

    assertDatabaseHas('users', [
        "name"  => "Divino",
        "email" => "divino@divino.com",
    ]);

    $divino = User::whereEmail('divino@divino.com')->first();

    assertTrue(Hash::check('password', $divino->password));
});

it("should log the new user in the system", function () {
    postJson(route('register'), [
        "name"               => "Divino",
        "email"              => "divino@divino.com",
        "email_confirmation" => "divino@divino.com",
        "password"           => "password",
    ])->assertOk();

    $user = User::first();

    assertAuthenticatedAs($user);
});

describe("validations", function () {

    test('name', function ($rule, $value, $meta = []) {
        postJson(route('register'), ['name' => $value])
            ->assertJsonValidationErrors([
                'name' => __(
                    'validation.' . $rule,
                    array_merge(['attribute' => 'name'], $meta)
                ),
            ]);
    })->with([
        'required' => ['required', ''],
        'min:3'    => ['min', 'AB', ['min' => 3]],
        'max'      => ['max', str_repeat('*', 256), ['max' => 255]],
    ]);

    test('email', function ($rule, $value, $meta = []) {
        if($rule == "unique") {
            User::factory()->create(['email' => $value]);
        }

        postJson(route('register'), ['email' => $value])
            ->assertJsonValidationErrors([
                'email' => __(
                    'validation.' . $rule,
                    array_merge(['attribute' => 'email'], $meta)
                ),
            ]);
    })->with([
        'required'  => ['required', ''],
        'min:3'     => ['min', 'AB', ['min' => 3]],
        'max'       => ['max', str_repeat('*', 256), ['max' => 255]],
        'email'     => ['email', 'not-email'],
        'unique'    => ['unique', 'divino@divino.com'],
        'confirmed' => ['confirmed', 'divino@divino.com'],
    ]);

    test('password', function ($rule, $value, $meta = []) {
        postJson(route('register'), ['password' => $value])
            ->assertJsonValidationErrors([
                'password' => __(
                    'validation.' . $rule,
                    array_merge(['attribute' => 'password'], $meta)
                ),
            ]);
    })->with([
        'required' => ['required', ''],
        'min:8'    => ['min', 'AB', ['min' => 8]],
        'max:40'   => ['max', str_repeat('*', 41), ['max' => 40]],
    ]);
});

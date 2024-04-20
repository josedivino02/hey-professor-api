<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

it("Should be able to register in the application", function () {
    postJson(route('register'), [
        "name"     => "Divino",
        "email"    => "divino@divino.com",
        "password" => "password",
    ])->assertOk();

    assertDatabaseHas('users', [
        "name"  => "Divino",
        "email" => "divino@divino.com",
    ]);

    $divino = User::whereEmail('divino@divino.com')->first();

    assertTrue(Hash::check('password', $divino->password));
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
});

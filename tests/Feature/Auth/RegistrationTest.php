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
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('users', [
        "name"  => "Divino",
        "email" => "divino@divino.com",
    ]);

    $divino = User::whereEmail('divino@divino.com')->first();

    assertTrue(Hash::check('password', $divino->password));
});

<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{actingAs, assertGuest, postJson};

it("Should be able to logout", function () {
    $user = User::factory()->create(['email' => 'divino@divino.com', 'password' => Hash::make('password')]);

    actingAs($user);

    postJson(route('logout'))->assertNoContent();

    assertGuest();
});

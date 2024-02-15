<?php

use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use Laravel\Sanctum\Sanctum;

it("should be able to store a new question", function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum Divino?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id' => $user->id,
        'question' => 'Lorem ipsum Divino?',
    ]);
});

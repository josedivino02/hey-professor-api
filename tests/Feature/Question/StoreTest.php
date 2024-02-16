<?php

use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;use Laravel\Sanctum\Sanctum;

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

test("after creating a new question. I need to make sure that it creates on _draft status", function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum Divino?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id' => $user->id,
        'status' => 'draft',
        'question' => 'Lorem ipsum Divino?',
    ]);
});

describe("validation rules", function () {
    test("question::required", function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', []))
            ->assertJsonValidationErrors([
                'question' => 'required',
            ]);
    });

    test("question::ending with question mark", function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Question without a question mark',
        ]))
            ->assertJsonValidationErrors([
                'question' => 'The question should end with question mark (?).',
            ]);
    });

    test("question::min characters should be 10", function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Question?',
        ]))
            ->assertJsonValidationErrors([
                'question' => 'least 10 characters',
            ]);
    });
});

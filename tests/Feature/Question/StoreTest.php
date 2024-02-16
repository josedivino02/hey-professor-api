<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, postJson};

it("should be able to store a new question", function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum Divino?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'question' => 'Lorem ipsum Divino?',
    ]);
});

test("with the creation of the question. we need to make sure that it creates with status _draft_", function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum Divino?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'status'   => 'draft',
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

    test("question::should be unique", function () {
        $user = User::factory()->create();
        Question::factory()->create([
            'question' => 'Lorem ipsum Divino?',
            'status'   => 'draft',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Lorem ipsum Divino?',
        ]))
            ->assertJsonValidationErrors([
                'question' => 'already been taken',
            ]);
    });
});

test("after creating we should return a status 201 with the created question", function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $request = postJson(route('questions.store', [
        'question' => 'Lorem ipsum Divino?',
    ]))->assertCreated();

    $question = Question::latest()->first();

    $request->assertJson([
        'data' => [
            'id'         => $question->id,
            'question'   => $question->question,
            'status'     => $question->status,
            'created_by' => [
                'id'   => $user->id,
                'name' => $user->name,
            ],
            'created_at' => $question->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $question->updated_at->format('Y-m-d h:i:s'),
        ],
    ]);
});

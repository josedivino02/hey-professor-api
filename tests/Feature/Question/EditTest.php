<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

it("should be able to update a question", function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    putJson(route('questions.update', $question), [
        'question' => 'Updating question?',
    ])->assertOk();

    assertDatabaseHas('questions', [
        'id'       => $question->id,
        'user_id'  => $user->id,
        'question' => 'Updating question?',
    ]);
});

describe("validation rules", function () {
    test("question::required", function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => '',
        ])->assertJsonValidationErrors([
            'question' => 'required',
        ]);
    });

    test("question::ending with question mark", function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question should have a mark',
        ])->assertJsonValidationErrors([
            'question' => 'The question should end with question mark (?).',
        ]);
    });

    test("question::min characters should be 10", function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question?',
        ])->assertJsonValidationErrors([
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

        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Lorem ipsum Divino?',
        ])->assertJsonValidationErrors([
            'question' => 'already been taken',
        ]);
    });

    test("question::should be unique only if id is different", function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'question' => 'Lorem ipsum Divino?',
            'user_id'  => $user->id,
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Lorem ipsum Divino?',
        ])->assertOk();
    });

    test("question::should be able to edit only if the status is in draft", function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'published']);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question should have a mark?',
        ])->assertJsonValidationErrors([
            'question' => 'The question should be a draft to be able to edit.',
        ]);
    });
});

describe("security", function () {
    test("only the person who create the question can update the same question", function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $question = Question::factory()->create(['user_id' => $user1->id]);

        Sanctum::actingAs($user2);

        putJson(route('questions.update', $question), [
            'question' => 'updating the question?',
        ])->assertForbidden();

        assertDatabaseHas('questions', [
            'id'       => $question->id,
            'question' => $question->question,
        ]);

    });
});

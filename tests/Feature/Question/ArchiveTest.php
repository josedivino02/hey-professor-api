<?php

use App\Models\Question;
use App\Models\User;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;use Laravel\Sanctum\Sanctum;

it("should be able to archive a question", function () {
    $user = User::factory()->create();

    $question = Question::factory()->for($user)->create();

    Sanctum::actingAs($user);

    deleteJson(route('questions.archive', $question))
        ->assertNoContent();

    assertSoftDeleted('questions', ['id' => $question->id]);
});

it("should allow that only the creator can archive", function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    $question = Question::factory()->for($user)->create();

    Sanctum::actingAs($user2);

    deleteJson(route('questions.archive', $question))
        ->assertForbidden();

    assertNotSoftDeleted('questions', ['id' => $question->id]);
});

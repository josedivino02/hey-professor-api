<?php

use App\Models\Question;
use App\Models\User;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use Laravel\Sanctum\Sanctum;

it("should be able to delete a question", function () {
    $user = User::factory()->create();

    $question = Question::factory()->for($user)->create();

    Sanctum::actingAs($user);

    deleteJson(route('questions.delete', $question))
        ->assertNoContent();

    assertDatabaseMissing('question', ['id' => $question->id]);
});

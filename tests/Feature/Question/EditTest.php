<?php

use App\Models\Question;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;
use Laravel\Sanctum\Sanctum;

it("should be able to update a question", function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    putJson(route('questions.update', $question), [
        'question' => 'Updating question?',
    ])->assertOk();

    assertDatabaseHas('questions', [
        'id' => $question->id,
        'user_id' => $user->id,
        'question' => 'Updating question?',
    ]);
});

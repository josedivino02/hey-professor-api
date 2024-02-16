<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question;

class DeleteController extends Controller
{
    public function __invoke(Question $question)
    {
        $this->authorize('forceDelete', $question);
        $question->forceDelete();

        return response()->noContent();
    }
}

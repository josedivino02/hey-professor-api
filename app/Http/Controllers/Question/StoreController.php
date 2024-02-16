<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $question = Question::create([
            'user_id' => auth()->user()->id,
            'status' => 'draft',
            'question' => $request->question,
        ]);

        return QuestionResource::make($question);
    }
}

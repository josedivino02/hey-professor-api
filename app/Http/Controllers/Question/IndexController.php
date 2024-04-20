<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = request()->q;

        $questions = Question::query()
        ->published()
        ->search(request()->q)
        ->withSum('votes', 'like')
        ->withSum('votes', 'unlike')
        ->get();

        return  QuestionResource::collection($questions);
    }
}

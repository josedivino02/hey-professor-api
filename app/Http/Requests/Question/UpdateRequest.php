<?php

namespace App\Http\Requests\Question;

use App\Rules\{OnlyAsDraft, WithQuestionMark};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route()->question);
    }

    public function rules(): array
    {
        return [
            'question' => [
                'required',
                new WithQuestionMark(),
                new OnlyAsDraft($this->route()->question),
                'min:10',
                Rule::unique('questions')->ignore($this->route()->question->id),
            ],
        ];
    }
}

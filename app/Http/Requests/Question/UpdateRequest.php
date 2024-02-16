<?php

namespace App\Http\Requests\Question;

use App\Rules\WithQuestionMark;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', new WithQuestionMark, 'min:10', Rule::unique('questions')->ignore($this->route()->question->id)],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vocabulary_number'=>'required|numeric',
            'type_search'=>'required',
            'quiz_date'=>'required_if:type_search,"Date" ||required_if:isThemeGrammar,true',
            'theme'=>'required_if:type_search,"Theme"',
            'isThemeGrammar'=>'nullable',
            'answer_language'=>'required_if:isThemeGrammar,false'
        ];
    }
}

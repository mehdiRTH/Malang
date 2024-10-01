<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class VocabularyRequest extends FormRequest
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
        if($this->createByUpload)
        {
            return [
                'sheet'=>['required',File::types(['xlsx'])],
                'name'=>'nullable',
                'translations'=>'nullable'
            ];
        }
        else {
            return [
                'name'=>'required',
                'translations'=>'required|array',
                'sheet'=>'nullable'
            ];
        }

    }
}

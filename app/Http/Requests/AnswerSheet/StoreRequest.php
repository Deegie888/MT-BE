<?php

namespace App\Http\Requests\AnswerSheet;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'category' => 'required',
            'level' => 'required',
            'answer' => 'required',
            'hint1' => 'nullable',
            'hint2' => 'nullable',
            'hint3' => 'nullable',
            'image' => 'required',
            'description' => 'required'
        ];
    }
}

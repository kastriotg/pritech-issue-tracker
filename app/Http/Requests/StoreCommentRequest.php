<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Always authorizes the request.
     *
     * @return bool Always `true`.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'issue_id' => ['required', 'integer', 'exists:issues,id'],
            'author_name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:255'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRulesRequest extends FormRequest
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
            'message' => 'required|string|max:255',
            'uuid.*' => 'nullable|string|exists:rules,uuid',
            'action.*' => 'required|in:show,hide',
            'rule.*' => 'required|in:contains,starts_with,ends_with,exact',
            'url.*' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'The message text is required.',
            'message.string' => 'The message text must be a valid string.',
            'message.max' => 'The message text must not exceed 255 characters.',
            'action.*.required' => 'The action field is required.',
            'action.*.in' => 'The action must be either "show" or "hide".',
            'rule.*.required' => 'The condition field is required.',
            'rule.*.in' => 'The condition must be one of the following: contains, starts with, ends with, exact.',
            'url.*.required' => 'The URL part field is required.',
            'url.*.string' => 'The URL part must be a string.',
            'url.*.max' => 'The URL part must not exceed 255 characters.'
        ];
    }
}

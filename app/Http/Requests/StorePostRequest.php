<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'                 => 'nullable|string|max:255',
            'content'               => 'nullable|string',
            'privacy_settings'      => 'nullable|in:1,2,3',
            // 'images'            => 'nullable|array|min:1',
            // 'images.*'          => 'image|mimes:jpeg,png,jpg,gif',
        ];
    }
}

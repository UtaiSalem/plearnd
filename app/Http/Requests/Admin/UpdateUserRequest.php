<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'staff', 'requester'])],
            'user_type' => ['required_if:role,requester', 'nullable', Rule::in(['student', 'employee'])],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'student_id' => ['required_if:user_type,student', 'nullable', 'string', 'max:20'],
            'employee_id' => ['required_if:user_type,employee', 'nullable', 'string', 'max:20'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_type' => ['required', Rule::in(['student', 'employee'])],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:'.User::class],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'student_id' => [$request->user_type === 'student' ? 'required' : 'nullable', 'string', 'max:30'],
            'employee_id' => [$request->user_type === 'employee' ? 'required' : 'nullable', 'string', 'max:30'],
            'faculty' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'default_advisor' => [$request->user_type === 'student' ? 'required' : 'nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'requester',
            'user_type' => $data['user_type'],
            'student_id' => $data['student_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'faculty' => $data['faculty'],
            'department' => $data['department'],
            'phone' => $data['phone'],
            'default_advisor' => $data['default_advisor'] ?? null,
            'password' => $data['password'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route($user->homeRoute(), absolute: false));
    }
}

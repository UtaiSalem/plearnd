<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input)
    {

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable','string', 'max:16', 'unique:users,phone_number'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            ])->validate();
    
        $suggester = User::where('personal_code', $input['suggester'])->first();

        if ($input['suggester'] != '11111111' && $suggester->no_of_ref >= 5 ){
            return redirect()->back()->withErrors([
                    'isSuggesterActive' => false,
                    'suggester' => $suggester,
                    'error'=>'Suggester has reached the maximum number of referrals/ผู้แนะนำได้รับจำนวนการแนะนำครบจำนวนแล้ว'
                ]
            )->withInput();
        }

        $new_user = User::create([
            'name'                  => $input['name'],
            'email'                 => $input['email'],
            'phone_number'          => $input['phone'],
            'personal_code'         => User::generatePersonalCode(),
            'reference_code'        => User::generateReferenceCode(),
            'suggester_code'        => $input['suggester'] ?? '11111111',
            'password'              => Hash::make($input['password']),
        ]);

        $suggester->increment('no_of_ref');

        return $new_user;
    }
}

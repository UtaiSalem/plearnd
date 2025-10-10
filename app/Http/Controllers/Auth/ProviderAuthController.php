<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\VisitorCounter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ProviderAuthController extends Controller
{
    public function redirectToGoogle($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleGoogleCallback($provider)
    {
        try {
            $provider_user = Socialite::driver($provider)->user();

            $user = User::Where($provider.'_id', $provider_user->id)
                            ->orWhere('email', $provider_user->email)
                            ->first();

            if ($user) {
                if ($user[$provider.'_id'] == null) {
                    $user[$provider.'_id'] = $provider_user->id;
                    $user->save();
                }
                VisitorCounter::first()->increment('login_counter');
                Auth::login($user);
                // return redirect('/dashboard');
                return redirect('/newsfeed');
            }else{
                return redirect('/');
            }
        
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

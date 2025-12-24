<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\Models\User;

class MicrosoftController extends Controller
{
    // Redirect to Microsoft login
    public function redirectToProvider()
    {
        return Socialite::driver('microsoft')
            ->stateless()
            ->with(['prompt' => 'select_account'])
            ->with(['prompt' => 'login'])
            ->redirect();
    }

    // Handle callback
    public function handleProviderCallback()
    {
        $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $microsoftUser->getEmail()],
            [
                'name' => $microsoftUser->getName(),
                'email_verified_at' => now(),
                // role is set manually in DB for admin, default is 'user'
            ]
        );

        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('resume.upload.form');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Resume;

class MicrosoftController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('microsoft')
            ->stateless()
            ->with(['prompt' => 'login'])
            ->redirect();
    }

    public function handleProviderCallback()
    {
        $microsoftUser = Socialite::driver('microsoft')
            ->stateless()
            ->user();

        $email = strtolower($microsoftUser->getEmail());

        // 🔒 Invite-only check
        $user = User::where('email', $email)->first();

        if (!$user || !$user->is_invited) {
            return redirect()
                ->route('login')
                ->with(
                    'invite_error',
                    'This is an Invite only Assessment, Kindly Contact the Assessment Team'
                );
        }

        // Update user details
        $user->update([
            'name' => $microsoftUser->getName(),
            'email_verified_at' => now(),
        ]);

        // Login
        Auth::login($user);

        // ---------- ROLE BASED REDIRECT ----------
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        if ($user->role === 'user') {
            $resume = Resume::where('user_id', $user->id)->latest()->first();

            // First login → Resume Upload
            if (!$resume) {
                return redirect()->route('resume.upload.form');
            }

            // Resume exists (valid or invalid) → Dashboard
            return redirect()->route('user.dashboard');
        }

        // Fallback safety
        Auth::logout();
        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\Models\User;

class MicrosoftController extends Controller
{
    /**
     * Redirect the user to Microsoft login page.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('microsoft')
            ->stateless()
            ->with(['prompt' => 'select_account']) // ensure account selection
            ->redirect();
    }

    /**
     * Handle Microsoft callback.
     * Only invited users can log in.
     */
    public function handleProviderCallback()
    {
        $microsoftUser = Socialite::driver('microsoft')->stateless()->user();

        $email = strtolower($microsoftUser->getEmail());

        // 🔒 Check if user exists and is invited
        $user = User::where('email', $email)->first();

        if (!$user || !$user->is_invited) {
            return redirect()
                ->route('login')
                ->with(
                    'invite_error',
                    'This is an Invite only Assessment, Kindly Contact the Assessment Team'
                );
        }

        // ✅ Update user details after first login
        $user->update([
            'name' => $microsoftUser->getName(),
            'email_verified_at' => now(),
        ]);

        // Log in the user
        
        Auth::login($user);

        // First-time login: check if resume exists (only for normal users)
        if ($user->role === 'user') {
            $resume = \App\Models\Resume::where('user_id', $user->id)->latest()->first();

            if (!$resume) {
                // First-time login → upload resume
                return redirect()->route('resume.upload.form');
            }

            // Returning user → dashboard
            return redirect()->route('user.dashboard');
        }

        // Admin → dashboard
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }
    }
}

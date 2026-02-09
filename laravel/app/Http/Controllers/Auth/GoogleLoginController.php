<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    /**
     * Redirect ke Google untuk login
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User sudah ada → login
                Auth::login($user, true);
            } else {

                // =============================
                // BUAT USERNAME OTOMATIS
                // =============================
                $baseUsername = Str::slug(
                    explode('@', $googleUser->getEmail())[0]
                );

                $username = $baseUsername;
                $counter = 1;

                // Pastikan username unik
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . '-' . $counter;
                    $counter++;
                }

                // =============================
                // CREATE USER
                // =============================
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);

                Auth::login($user, true);
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
}

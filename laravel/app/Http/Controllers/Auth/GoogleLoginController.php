<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User sudah ada — update avatar jika masih URL Google
                if ($user->avatar && Str::startsWith($user->avatar, ['http://', 'https://'])) {
                    $localAvatar = $this->downloadGoogleAvatar($googleUser->getAvatar(), $googleUser->getId());
                    if ($localAvatar) {
                        $user->avatar = $localAvatar;
                        $user->save();
                    }
                }

                Auth::login($user, true);
            } else {
                // Buat username otomatis
                $baseUsername = Str::slug(explode('@', $googleUser->getEmail())[0]);
                $username = $baseUsername;
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . '-' . $counter;
                    $counter++;
                }

                // Download avatar Google ke storage lokal
                $avatarPath = $this->downloadGoogleAvatar($googleUser->getAvatar(), $googleUser->getId());

                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'username'          => $username,
                    'email'             => $googleUser->getEmail(),
                    'password'          => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                    'google_id'         => $googleUser->getId(),
                    // Simpan path lokal, fallback ke URL Google jika download gagal
                    'avatar'            => $avatarPath ?? $googleUser->getAvatar(),
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
     * Download avatar dari Google dan simpan ke storage/app/public/avatars/
     * Return path relatif (avatars/xxx.jpg) atau null jika gagal
     */
    private function downloadGoogleAvatar(?string $googleAvatarUrl, string $googleId): ?string
    {
        if (!$googleAvatarUrl) return null;

        try {
            // Request gambar dari Google (tanpa ukuran kecil)
            // Google avatar URL defaultnya kecil, tambahkan size besar
            $url = preg_replace('/=s\d+(-c)?$/', '=s200-c', $googleAvatarUrl);

            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) return null;

            // Tentukan ekstensi dari Content-Type
            $contentType = $response->header('Content-Type');
            $extension = match(true) {
                str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            // Nama file unik berdasarkan google_id
            $filename = 'avatars/google_' . $googleId . '.' . $extension;

            // Simpan ke storage/app/public/avatars/
            Storage::disk('public')->put($filename, $response->body());

            return $filename; // path relatif: "avatars/google_xxx.jpg"

        } catch (\Exception $e) {
            \Log::warning('Gagal download avatar Google: ' . $e->getMessage());
            return null;
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
}
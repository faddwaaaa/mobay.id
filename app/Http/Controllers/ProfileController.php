<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;

class ProfileController extends Controller
{
    /**
     * Display the user's profile information page.
     * Shows user profile details like avatar, name, email, verification status, login method, and join date.
     */
    public function profile(Request $request): View
    {
        return view('dashboard.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // update basic fields
        $user->fill($request->validated());

        // upload avatar baru (kalau ada)
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');

            /**
             * ===== STORAGE VALIDATION =====
             * Check kapasitas penyimpanan sebelum upload avatar
             */
            $storageValidation = $user->canUpload($avatarFile->getSize());
            if (!$storageValidation['can_upload']) {
                return Redirect::route('dashboard.profile')
                    ->with('error', $storageValidation['message'])
                    ->withInput();
            }

            // hapus avatar lama JIKA dari storage & kurangi storage usage
            if ($user->avatar && !Str::startsWith($user->avatar, ['http://','https://'])) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    $oldFileSize = Storage::disk('public')->size($user->avatar);
                    Storage::disk('public')->delete($user->avatar);
                    // Kurangi storage usage saat avatar lama dihapus
                    $user->removeStorageUsage($oldFileSize);
                }
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            
            // Tambahkan storage usage setelah avatar baru diupload
            $user->addStorageUsage($avatarFile->getSize());
        }

        // email berubah → unverified
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('dashboard.profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!is_null($user->google_id)) {
            // User Google — validasi via email
            $request->validateWithBag('userDeletion', [
                'email_confirm' => ['required', 'string'],
            ]);

            if ($request->email_confirm !== $user->email) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email_confirm' => 'Email yang dimasukkan tidak cocok dengan akun Anda.',
                ])->errorBag('userDeletion');
            }
        } else {
            // User manual — validasi via password
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function preview($username)
    {
        // Get user with pages and blocks
        // 🔥 PENTING: Load product relationship
        $user = User::where('username', $username)
            ->with([
                'pages.blocks' => function ($query) {
                    $query->orderBy('position');
                },
                'pages.blocks.product.images' // Load product dan images-nya
            ])
            ->firstOrFail();

        // Get selected page from query parameter
        $pageId = request()->query('page');
        
        if ($pageId) {
            $page = $user->pages->where('id', $pageId)->first();
        } else {
            $page = $user->pages->first();
        }

        // Get all products for modal
        $products = Product::with('images')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('preview', compact('user', 'page', 'products'));
    }

    /**
     * Show public profile
     * Route: /{username}
     */
    public function show($username)
    {
        // Get user with pages and blocks
        // 🔥 PENTING: Load product relationship
        $user = User::where('username', $username)
            ->with([
                'pages.blocks' => function ($query) {
                    $query->orderBy('position');
                },
                'pages.blocks.product.images' // Load product dan images-nya
            ])
            ->firstOrFail();

        // Get all products for display
        $products = Product::with('images')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'products'));
    }

    /**
     * Show specific page
     * Route: /{username}/{pageSlug}
     */
    public function showPage($username, $pageSlug)
    {
        $user = User::where('username', $username)
            ->with([
                'pages.blocks' => function ($query) {
                    $query->orderBy('position');
                },
                'pages.blocks.product.images'
            ])
            ->firstOrFail();

        $page = $user->pages->where('slug', $pageSlug)->firstOrFail();

        $products = Product::with('images')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('profile.show', compact('user', 'page', 'products'));
    }
}

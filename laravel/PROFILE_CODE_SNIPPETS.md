# Code Snippets & Reference - Halaman Profil Pengguna

## 📌 Quick Links

Gunakan code snippets ini untuk berbagai keperluan terkait halaman profil.

---

## 🔗 Link & Navigation

### Membuat Link ke Profil (Blade)
```blade
<!-- Link text -->
<a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-800">
    Lihat Profil Saya
</a>

<!-- Avatar clickable to profile -->
<a href="{{ route('profile.show') }}" class="block">
    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
         alt="Profil" 
         class="w-10 h-10 rounded-full">
</a>

<!-- Button to profile -->
<a href="{{ route('profile.show') }}" 
   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    View Profile
</a>
```

### Redirect ke Profil (PHP Controller)
```php
// Simple redirect
return redirect()->route('profile.show');

// With flash message
return redirect()->route('profile.show')
    ->with('success', 'Profile updated successfully!');

// With query parameter (if needed)
return redirect()->route('profile.show')
    ->with('tab', 'settings');
```

---

## 👤 Menampilkan Informasi User

### User Name
```blade
<!-- Blade -->
<h1 class="text-3xl font-bold">{{ Auth::user()->name }}</h1>
<h1 class="text-3xl font-bold">{{ $user->name }}</h1>

<!-- PHP -->
echo Auth::user()->name;
echo $user->name;
```

### User Email
```blade
<!-- Blade -->
<p class="text-gray-700">{{ Auth::user()->email }}</p>
<p class="text-gray-700">{{ $user->email }}</p>
```

### Email Verification Status
```blade
<!-- Check if verified -->
@if (Auth::user()->email_verified_at)
    <span class="text-green-600">✓ Email Verified</span>
@else
    <span class="text-yellow-600">⚠ Pending Verification</span>
@endif

<!-- With date -->
@if ($user->email_verified_at)
    <span>Verified on {{ $user->email_verified_at->format('d M Y') }}</span>
@endif

<!-- PHP Controller -->
if ($user->email_verified_at) {
    // Email is verified
}
```

---

## 🔐 Login Method (Google vs Email)

### Display Login Method
```blade
<!-- Check if Google login -->
@if ($user->google_id)
    <span class="badge badge-red">Google Sign-in</span>
@else
    <span class="badge badge-blue">Email & Password</span>
@endif

<!-- With icon -->
@if ($user->google_id)
    <svg class="w-5 h-5 text-red-600"><!-- Google icon --></svg>
    <span>Google</span>
@else
    <svg class="w-5 h-5 text-blue-600"><!-- Email icon --></svg>
    <span>Email</span>
@endif
```

### PHP Check
```php
// In controller
if ($user->google_id) {
    $loginMethod = 'Google';
} else {
    $loginMethod = 'Email & Password';
}

// In view
{{ $user->google_id ? 'Google' : 'Email' }}
```

---

## 📸 Avatar Handling

### Display Avatar with Fallback
```blade
<!-- Simple image -->
@if ($user->avatar)
    <img src="{{ asset('storage/' . $user->avatar) }}" 
         alt="{{ $user->name }}"
         class="w-32 h-32 rounded-lg object-cover">
@else
    <!-- Default avatar icon -->
    <div class="w-32 h-32 bg-blue-600 rounded-lg flex items-center justify-center">
        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
    </div>
@endif

<!-- With link -->
<a href="{{ route('profile.show') }}">
    @if ($user->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" 
             alt="{{ $user->name }}"
             class="w-10 h-10 rounded-full object-cover hover:opacity-80">
    @else
        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-bold">{{ substr($user->name, 0, 1) }}</span>
        </div>
    @endif
</a>
```

### Upload Avatar (Future)
```blade
<form action="{{ route('profile.updateAvatar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="avatar" accept="image/*" required>
    <button type="submit">Upload Avatar</button>
</form>
```

```php
// In Controller
public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);
    
    // Delete old avatar if exists
    if ($request->user()->avatar) {
        Storage::disk('public')->delete($request->user()->avatar);
    }
    
    // Store new avatar
    $path = $request->file('avatar')->store('avatars', 'public');
    
    $request->user()->update(['avatar' => $path]);
    
    return redirect()->route('profile.show')
        ->with('success', 'Avatar updated successfully!');
}
```

---

## 📅 Date Formatting

### Join Date
```blade
<!-- Full date -->
<p>{{ $user->created_at->format('d F Y') }}</p>
<!-- Output: 15 January 2026 -->

<!-- Short date -->
<p>{{ $user->created_at->format('d/m/Y') }}</p>
<!-- Output: 15/01/2026 -->

<!-- With time -->
<p>{{ $user->created_at->format('d F Y, H:i') }}</p>
<!-- Output: 15 January 2026, 14:30 -->

<!-- Humanized (relative) -->
<p>{{ $user->created_at->diffForHumans() }}</p>
<!-- Output: 14 days ago -->

<!-- Combined -->
<div>
    <p class="font-bold">{{ $user->created_at->format('d F Y') }}</p>
    <p class="text-gray-600 text-sm">{{ $user->created_at->diffForHumans() }}</p>
</div>

<!-- Email verified date -->
<p>Verified on {{ $user->email_verified_at->format('d F Y, H:i') }}</p>
```

### Date Localization (Indonesian)
```blade
<!-- Using Laravel localization -->
<p>{{ $user->created_at->isoFormat('DD MMMM YYYY') }}</p>
<!-- Requires: composer require nesbot/carbon:^2.0 -->

<!-- Or custom locale -->
@php
    \Carbon\Carbon::setLocale('id');
    $date = $user->created_at->translatedFormat('d F Y');
@endphp
<p>{{ $date }}</p>
```

---

## 🎨 Status Badges

### Verification Status Badge
```blade
<!-- Green badge - Verified -->
<span class="inline-flex items-center px-3 py-1 rounded-full 
             bg-green-100 text-green-800 text-sm font-semibold">
    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
    </svg>
    Verified
</span>

<!-- Yellow badge - Pending -->
<span class="inline-flex items-center px-3 py-1 rounded-full 
             bg-yellow-100 text-yellow-800 text-sm font-semibold">
    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
    </svg>
    Not Verified
</span>
```

### Login Method Badge
```blade
<!-- Google -->
<span class="inline-flex items-center px-3 py-1 rounded-full 
             bg-red-100 text-red-800 text-sm font-semibold">
    <svg class="w-4 h-4 mr-1" fill="currentColor"><!-- Google logo --></svg>
    Google Sign-in
</span>

<!-- Email -->
<span class="inline-flex items-center px-3 py-1 rounded-full 
             bg-blue-100 text-blue-800 text-sm font-semibold">
    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
    </svg>
    Email & Password
</span>
```

---

## 🔐 Authentication Checks

### Check if User is Authenticated
```php
// In Blade
@auth
    <p>Logged in as {{ Auth::user()->name }}</p>
@endauth

@guest
    <p>You are not logged in</p>
@endguest

// In PHP
if (Auth::check()) {
    // User is logged in
}

if (Auth::guest()) {
    // User is not logged in
}

// Get current user
$user = Auth::user(); // Returns User model or null
$user = auth()->user(); // Alternative syntax
```

### Middleware Protection
```php
// In routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
});

// In Controller
class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Alternative approach
    }
    
    public function show(Request $request)
    {
        // $request->user() available here
        $user = $request->user();
    }
}
```

---

## 📊 Statistics & Aggregations

### Count Related Models (Future)
```php
// In ProfileController
$linkCount = $user->links()->count();
$totalClicks = $user->links()
    ->withCount('clicks')
    ->get()
    ->sum('clicks_count');

return view('profile.show', [
    'user' => $user,
    'linkCount' => $linkCount,
    'totalClicks' => $totalClicks,
]);
```

### Display Statistics in Blade
```blade
<div class="grid grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $linkCount }}</p>
        <p class="text-sm text-gray-600">Total Links</p>
    </div>
    
    <div class="bg-white p-4 rounded text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $totalClicks }}</p>
        <p class="text-sm text-gray-600">Total Clicks</p>
    </div>
    
    <div class="bg-white p-4 rounded text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $user->created_at->diffInDays(now()) }}</p>
        <p class="text-sm text-gray-600">Days Active</p>
    </div>
</div>
```

---

## 🎯 Reusable Components

### User Info Card Component
```blade
<!-- Create: resources/views/components/user-info-card.blade.php -->
@props(['user', 'showEdit' => true])

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex items-center space-x-4">
        @if ($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" 
                 alt="{{ $user->name }}"
                 class="w-16 h-16 rounded-full object-cover">
        @else
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-lg font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
        @endif
        
        <div class="flex-1">
            <h3 class="text-xl font-bold">{{ $user->name }}</h3>
            <p class="text-gray-600">{{ $user->email }}</p>
        </div>
        
        @if ($showEdit)
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>
        @endif
    </div>
</div>

<!-- Usage -->
<x-user-info-card :user="$user" :showEdit="true" />
```

### Profile Section Component
```blade
<!-- Create: resources/views/components/profile-section.blade.php -->
@props(['title', 'icon', 'value', 'badge' => null])

<div class="space-y-2">
    <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
        @if ($icon)
            <span class="inline-flex items-center">
                {!! $icon !!}
                <span class="ml-2">{{ $title }}</span>
            </span>
        @else
            {{ $title }}
        @endif
    </label>
    
    <div class="flex items-center space-x-3">
        <p class="text-lg text-gray-900 font-medium">{{ $value }}</p>
        @if ($badge)
            {{ $badge }}
        @endif
    </div>
</div>

<!-- Usage -->
<x-profile-section 
    title="Email Verification"
    :badge="$verificationBadge"
    value="{{ $user->email }}"
/>
```

---

## ✉️ Email Verification Helpers

### Send Verification Email
```php
// Send verification notification
$user->sendEmailVerificationNotification();

// In controller
public function resendVerification(Request $request)
{
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('profile.show');
    }
    
    $request->user()->sendEmailVerificationNotification();
    
    return back()->with('status', 'Verification link sent!');
}
```

### Check Verification Status
```blade
@if ($user->hasVerifiedEmail())
    <p class="text-green-600">Email is verified</p>
@else
    <p class="text-yellow-600">Email is not verified</p>
    <a href="{{ route('verification.send') }}" class="text-blue-600 hover:underline">
        Resend verification email
    </a>
@endif
```

---

## 🧪 Testing Examples

### Test Route Access
```php
// tests/Feature/ProfileTest.php
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_authenticated_user_can_view_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/profile');
        
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }
    
    public function test_unauthenticated_user_cannot_view_profile()
    {
        $response = $this->get('/profile');
        
        $response->assertRedirect('/login');
    }
}
```

---

## 🚨 Common Mistakes & Fixes

### Mistake 1: Using wrong user reference
```blade
<!-- ❌ WRONG -->
<p>{{ $user->avatar ?? 'default' }}</p> <!-- $user might not exist -->

<!-- ✅ CORRECT -->
<p>{{ Auth::user()->avatar ?? 'default' }}</p>
```

### Mistake 2: Not protecting routes
```php
// ❌ WRONG
Route::get('/profile', [ProfileController::class, 'show']);

// ✅ CORRECT
Route::middleware(['auth'])->get('/profile', [ProfileController::class, 'show']);
```

### Mistake 3: Wrong date format
```blade
<!-- ❌ WRONG (database format) -->
<p>{{ $user->created_at }}</p> <!-- 2026-01-15 10:30:45 -->

<!-- ✅ CORRECT (formatted) -->
<p>{{ $user->created_at->format('d F Y') }}</p> <!-- 15 January 2026 -->
```

### Mistake 4: Not checking null avatar
```blade
<!-- ❌ WRONG -->
<img src="{{ asset('storage/' . $user->avatar) }}"> <!-- Breaks if null -->

<!-- ✅ CORRECT -->
@if ($user->avatar)
    <img src="{{ asset('storage/' . $user->avatar) }}">
@endif
```

---

## 📖 Related Documentation

- [Laravel Authentication](https://laravel.com/docs/authentication)
- [Blade Conditional](https://laravel.com/docs/blade#if-statements)
- [Date Formatting](https://carbon.nesbot.com/docs/#localization)
- [Storage](https://laravel.com/docs/filesystem)

---

**Last Updated**: 29 January 2026
**Version**: 1.0


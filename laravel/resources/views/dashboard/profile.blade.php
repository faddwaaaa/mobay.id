@extends('layouts.dashboard')

@section('content')
<div class="profile-page">
    <div class="max-w-3xl mx-auto">
        <div class="profile-card">
            <div class="profile-header"></div>

            <div class="profile-content">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="profile-avatar">
                            <img src="{{ asset('storage/' . ($user->avatar ?? 'default.png')) }}">
                        </div>

                        <div>
                            <h1 class="profile-name">{{ $user->name }}</h1>
                            <p class="profile-email">{{ $user->email }}</p>
                        </div>
                    </div>

                    <a href="/profile/edit" class="btn-primary">
                        ✏️ Edit Profil
                    </a>
                </div>

                <div class="profile-info">
                    <div class="profile-item">
                        <label>Email</label>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="profile-item">
                        <label>Verifikasi</label>
                        <span class="badge badge-success">Terverifikasi</span>
                    </div>

                    <div class="profile-item">
                        <label>Login</label>
                        <span class="badge badge-google">Google</span>
                    </div>

                    <div class="profile-item">
                        <label>Bergabung</label>
                        <p>{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="profile-note">
                    <strong>Catatan:</strong> Klik Edit Profil untuk mengubah data akun.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')

<div class="profile-page">
    <div class="max-w-3xl mx-auto">
        @if (session('status') === 'profile-updated')
            <div class="alert-success mb-4">
                Profil berhasil diperbarui.
            </div>
        @endif
        <div class="profile-card">
            <div class="profile-header"></div>
            <div class="profile-content">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="profile-avatar">
                            @php
                                use Illuminate\Support\Str;
                            @endphp

                            @if ($user->avatar)
                                <img
                                    src="{{ Str::startsWith($user->avatar, ['http://', 'https://'])
                                            ? $user->avatar
                                            : asset('storage/'.$user->avatar) }}"
                                    alt="Avatar"
                                >
                            @else
                                <img src="{{ asset('img/default-avatar.jpg') }}" alt="Default Avatar">
                            @endif
                        </div>
                        <div class="profile-basic-info">
                            <h1 class="profile-name">{{ $user->name }}</h1>
                            <p class="profile-email">{{ $user->email }}</p>
                        </div>
                    </div>

                    <a href="/profile/edit" class="btn-primary">
                        <i class="fa-solid fa-pen"></i>Edit Profil
                    </a>
                </div>

                <div class="profile-info">
                    <div class="profile-item">
                        <label>Nama Toko / Nama Pengguna</label>
                        <p>{{ $user->username }}</p>
                    </div>
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
                        <label>Bergabung</label>
                        <p>{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="profile-note">
                    <strong>Catatan:</strong> Klik Edit Profil untuk mengubah data akun.
                </div>

                <a href="{{ route('premium.index') }}" class="premium-spotlight" aria-label="Lihat paket Premium">
                    <div class="premium-spotlight-bg"></div>
                    <div class="premium-spotlight-content">
                        <div class="premium-chip">
                            <i class="fas fa-crown"></i>
                            Premium Seller
                        </div>
                        <h3>Naik kelas dengan tampilan toko yang lebih profesional</h3>
                        <p>
                            Aktifkan paket premium untuk meningkatkan kepercayaan pembeli, menonjolkan produk utama,
                            dan memperkuat branding jualan Anda.
                        </p>
                        <div class="premium-pricing">
                            <span>Rp50.000/bulan</span>
                            <span>Rp550.000/tahun</span>
                        </div>
                        <div class="premium-cta">
                            Lihat Perbandingan Free vs Premium
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .premium-spotlight {
        margin-top: 16px;
        display: block;
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        text-decoration: none;
        background: linear-gradient(132deg, #0f172a 0%, #1d4ed8 48%, #3b82f6 100%);
        border: 1px solid #1e40af;
        box-shadow: 0 14px 30px rgba(29, 78, 216, .25);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .premium-spotlight:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(29, 78, 216, .32);
    }
    .premium-spotlight-bg {
        position: absolute;
        right: -36px;
        top: -48px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.26) 0%, rgba(255,255,255,0) 72%);
    }
    .premium-spotlight-content { position: relative; z-index: 1; padding: 18px; color: #fff; }
    .premium-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.3);
        background: rgba(255,255,255,.14);
        margin-bottom: 10px;
    }
    .premium-chip i { color: #facc15; }
    .premium-spotlight h3 { margin: 0; font-size: 22px; line-height: 1.2; font-weight: 800; }
    .premium-spotlight p { margin: 10px 0 12px; font-size: 14px; line-height: 1.55; opacity: .96; max-width: 600px; }
    .premium-pricing { display: flex; gap: 8px; flex-wrap: wrap; }
    .premium-pricing span {
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.3);
        font-size: 12px;
        font-weight: 700;
    }
    .premium-cta {
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #fef3c7;
    }

    .dark .premium-spotlight {
        border-color: #1e3a8a;
        box-shadow: 0 14px 34px rgba(0,0,0,.4);
    }

    @media (max-width: 640px) {
        .premium-spotlight-content { padding: 14px; }
        .premium-spotlight h3 { font-size: 19px; }
    }
</style>
@endsection

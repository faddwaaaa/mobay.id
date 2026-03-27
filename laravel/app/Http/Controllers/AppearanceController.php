<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\EncodedImage;
use Intervention\Image\ImageManager;

class AppearanceController extends Controller
{
    // ─── Helper: ambil atau buat profile ───
    private function getOrCreateProfile()
    {
        $user    = Auth::user();
        $profile = $user->userProfile;

        if (!$profile) {
            $profile = UserProfile::create([
                'user_id'  => $user->id,
                'username' => $user->username,
            ]);
        }

        return [$user, $profile];
    }

    public function index()
    {
        [$user, $profile] = $this->getOrCreateProfile();
        $appearanceAccess = $user->appearanceAccess();

        return view('dashboard.appearance.index', compact('user', 'profile', 'appearanceAccess'));
    }

    public function preview()
    {
        $user    = auth()->user();
        $profile = $user->userProfile;
        $socialLinks = collect($profile?->social_links ?? [])->filter()->toArray();
        return view('dashboard.appearance.preview', compact('user', 'profile', 'socialLinks'));
    }

    public function save(Request $request)
    {
        [$user, $profile] = $this->getOrCreateProfile();
        $appearanceAccess = $user->appearanceAccess();

        $validated = $request->validate([
            // Profile Card
            'about'                 => 'nullable|string|max:500',
            'text_color'            => 'nullable|string|max:20',
            'social_links'          => 'nullable|array',
            'banner_image'          => 'nullable|string|max:255',
            // Background
            'bg_type'               => 'nullable|in:color,gradient,image',
            'background_color'      => 'nullable|string|max:20',
            'bg_gradient_start'     => 'nullable|string|max:20',
            'bg_gradient_end'       => 'nullable|string|max:20',
            'bg_gradient_direction' => 'nullable|string|max:30',
            'bg_image'              => 'nullable|string|max:255',
            // Buttons
            'btn_style'             => 'nullable|in:fill,outline,hard_shadow,soft_shadow,ghost,minimal,neon,glass',
            'btn_shape'             => 'nullable|in:square,rounded,pill',
            'btn_color'             => 'nullable|string|max:20',
            'btn_text_color'        => 'nullable|string|max:20',
            'btn_glow_color'        => 'nullable|string|max:20',
            'btn_glow_bg'           => 'nullable|string|max:20',
            // Font
            'font_family'           => 'nullable|string|max:50',
            // Block Layout
            'block_layout'          => 'nullable|in:default,grid,compact,highlight',
        ]);

        if (! in_array($validated['bg_type'] ?? ($profile->bg_type ?? 'color'), $appearanceAccess['background_types'], true)) {
            $validated['bg_type'] = 'color';
            $validated['bg_image'] = null;
        }

        if (! in_array($validated['btn_style'] ?? ($profile->btn_style ?? 'fill'), $appearanceAccess['button_styles'], true)) {
            $validated['btn_style'] = 'fill';
        }

        if (! in_array($validated['font_family'] ?? ($profile->font_family ?? 'Plus Jakarta Sans'), $appearanceAccess['fonts'], true)) {
            $validated['font_family'] = 'Plus Jakarta Sans';
        }

        if (! in_array($validated['block_layout'] ?? ($profile->block_layout ?? 'default'), $appearanceAccess['block_layouts'], true)) {
            $validated['block_layout'] = 'default';
        }

        $profile->update($validated);
        $fresh = $profile->fresh();

        $shapeMap    = ['pill' => '50px', 'rounded' => '12px', 'square' => '4px'];
        $btnRadius   = $shapeMap[$fresh->btn_shape ?? 'rounded'] ?? '12px';
        $btnColor    = $fresh->btn_color      ?? '#3b82f6';
        $btnTxtColor = $fresh->btn_text_color ?? '#ffffff';
        $btnGlowColor = $fresh->btn_glow_color ?? '#38bdf8';
        $btnGlowBg = $fresh->btn_glow_bg ?? '#111827';

        return response()->json([
            'success' => true,
            'message' => 'Tampilan berhasil disimpan!',
            'broadcast_payload' => [
                'bgCss'          => $fresh->getBackgroundCss(),
                'bgColor'        => $fresh->getBackgroundColor(),
                'bgSize'         => $fresh->getBackgroundSize(),
                'fontFamily'     => $fresh->font_family,
                'textColor'      => $fresh->text_color ?? '#111827',
                'btnCss'         => $this->buildBtnCss($fresh->btn_style, $btnColor, $btnTxtColor, $btnGlowColor, $btnGlowBg),
                'btnRadius'      => $btnRadius,
                'btn_style'      => $fresh->btn_style,
                'btn_color'      => $btnColor,
                'btn_text_color' => $btnTxtColor,
                'btn_glow_color' => $btnGlowColor,
                'btn_glow_bg'    => $btnGlowBg,
                'block_layout'   => $fresh->block_layout ?? 'default',
            ],
        ]);
    }

    public function uploadBgImage(Request $request)
    {
        if (! $request->user()?->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'Background gambar hanya tersedia untuk akun Pro.',
            ], 403);
        }

        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096']);

        [$user, $profile] = $this->getOrCreateProfile();

        if ($profile->bg_image && !str_starts_with($profile->bg_image, 'wg_')) {
            Storage::disk('public')->delete($profile->bg_image);
        }

        $path = $this->storeCompressedBackground($request->file('image'));
        $profile->update(['bg_image' => $path, 'bg_type' => 'image']);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'path'    => $path,
        ]);
    }

    public function uploadBanner(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072']);

        [$user, $profile] = $this->getOrCreateProfile();

        if ($profile->banner_image) {
            Storage::disk('public')->delete($profile->banner_image);
        }

        $path = $request->file('image')->store('banners', 'public');
        $profile->update(['banner_image' => $path]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'path'    => $path,
        ]);
    }

    public function deleteBanner()
    {
        [$user, $profile] = $this->getOrCreateProfile();

        if (!$profile->banner_image) {
            return response()->json(['success' => false, 'message' => 'Tidak ada banner untuk dihapus.']);
        }

        Storage::disk('public')->delete($profile->banner_image);
        $profile->update(['banner_image' => null]);

        return response()->json(['success' => true, 'message' => 'Banner berhasil dihapus.']);
    }

    public function deleteBg(Request $request)
    {
        try {
            $profile = auth()->user()->userProfile ?? auth()->user()->profile;

            if (!$profile) {
                return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
            }

            if ($profile->bg_image && !str_starts_with($profile->bg_image, 'wg_')) {
                $filePath = storage_path('app/public/' . $profile->bg_image);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $profile->bg_image = null;
            $profile->bg_type  = 'color';
            $profile->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reset()
    {
        [$user, $profile] = $this->getOrCreateProfile();

        if ($profile->bg_image && !str_starts_with($profile->bg_image, 'wg_')) {
            Storage::disk('public')->delete($profile->bg_image);
        }

        $profile->update([
            'about'                 => null,
            'text_color'            => '#111827',
            'social_links'          => null,
            'bg_type'               => 'color',
            'background_color'      => '#ffffff',
            'bg_gradient_start'     => null,
            'bg_gradient_end'       => null,
            'bg_gradient_direction' => 'to bottom',
            'bg_image'              => null,
            'btn_style'             => 'fill',
            'btn_shape'             => 'rounded',
            'btn_color'             => '#3b82f6',
            'btn_text_color'        => '#ffffff',
            'btn_glow_color'        => '#38bdf8',
            'btn_glow_bg'           => '#111827',
            'font_family'           => 'Plus Jakarta Sans',
            'block_layout'          => 'default',
        ]);

        return response()->json(['success' => true, 'message' => 'Tampilan direset ke default.']);
    }

    private function buildBtnCss(?string $style, string $btnColor, string $btnTxtColor, ?string $btnGlowColor = null, ?string $btnGlowBg = null): string
    {
        $glowColor = $btnGlowColor ?: '#38bdf8';
        $glowBg = $btnGlowBg ?: '#111827';

        return match ($style) {
            'outline'     => "background:transparent;color:{$btnColor};border:2px solid {$btnColor};",
            'hard_shadow' => "background:{$btnColor};color:{$btnTxtColor};border:2px solid #111;box-shadow:3px 3px 0 #111;",
            'soft_shadow' => "background:{$btnColor};color:{$btnTxtColor};border:none;box-shadow:0 4px 16px rgba(0,0,0,0.15);",
            'ghost'       => "background:rgba(255,255,255,0.15);color:{$btnTxtColor};border:1.5px solid rgba(255,255,255,0.3);backdrop-filter:blur(8px);",
            'minimal'     => "background:transparent;color:{$btnColor};border:none;border-bottom:2px solid {$btnColor};border-radius:0!important;",
            'neon'        => "background:{$glowBg};color:{$btnTxtColor};border:2px solid {$glowColor};box-shadow:0 0 12px {$this->toRgba($glowColor, 0.45)},0 0 24px {$this->toRgba($glowColor, 0.25)};",
            'glass'       => "background:{$this->toRgba($btnColor, 0.12)};color:{$btnTxtColor};border:1px solid {$this->toRgba($btnColor, 0.28)};box-shadow:0 8px 20px {$this->toRgba($btnColor, 0.14)};backdrop-filter:blur(6px);",
            default       => "background:{$btnColor};color:{$btnTxtColor};border:2px solid {$btnColor};",
        };
    }

    private function toRgba(?string $color, float $alpha, string $fallback = '59,130,246'): string
    {
        if (! is_string($color)) {
            return "rgba({$fallback},{$alpha})";
        }

        $hex = ltrim(trim($color), '#');

        if (preg_match('/^[0-9a-fA-F]{3}$/', $hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return "rgba({$fallback},{$alpha})";
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    private function storeCompressedBackground($file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Keep uploaded backgrounds visually sharp, but lightweight enough for fast public loading.
        $image->scaleDown(width: 1600);

        /** @var EncodedImage $encoded */
        $encoded = $image->toWebp(72);

        $filename = 'bg_images/' . uniqid('bg_', true) . '.webp';
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}

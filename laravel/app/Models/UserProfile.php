<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'username',
        'bio',
        'avatar',
        'theme_color',
        'background_color',
        'is_public',
        'views',
        // Profile Card
        'banner_image',
        'about',
        'text_color',
        'social_links',
        // Appearance
        'bg_type',
        'bg_gradient_start',
        'bg_gradient_end',
        'bg_gradient_direction',
        'bg_image',
        'btn_style',
        'btn_shape',
        'btn_color',
        'btn_text_color',
        'font_family',
        'template',
    ];

    protected $casts = [
        'is_public'    => 'boolean',
        'social_links' => 'array',
    ];

    // ─── Relasi ───
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════
    // WALLPAPER MAP — sinkron dengan JS di blade
    // ═══════════════════════════════════════════
    private static array $wallpaperMap = [
        'wg_aurora'   => ['cssValue' => 'linear-gradient(135deg,#667eea 0%,#764ba2 50%,#f093fb 100%)'],
        'wg_peach'    => ['cssValue' => 'linear-gradient(135deg,#f6d365,#fda085)'],
        'wg_ocean'    => ['cssValue' => 'linear-gradient(135deg,#2193b0,#6dd5ed)'],
        'wg_forest'   => ['cssValue' => 'linear-gradient(135deg,#11998e,#38ef7d)'],
        'wg_candy'    => ['cssValue' => 'linear-gradient(135deg,#f953c6,#b91d73)'],
        'wg_golden'   => ['cssValue' => 'linear-gradient(135deg,#f7971e,#ffd200)'],
        'wg_royal'    => ['cssValue' => 'linear-gradient(135deg,#141e30,#243b55)'],
        'wg_rose'     => ['cssValue' => 'linear-gradient(135deg,#ff6a88,#ff99ac)'],
        'wg_nordic'   => ['cssValue' => 'linear-gradient(135deg,#a8edea,#fed6e3)'],
        'wg_twilight' => ['cssValue' => 'linear-gradient(135deg,#0f0c29,#302b63,#24243e)'],
        'wg_spring'   => ['cssValue' => 'linear-gradient(135deg,#96fbc4,#f9f586)'],
        'wg_dusk'     => ['cssValue' => 'linear-gradient(135deg,#2c3e50,#fd746c)'],
        'wg_dots'     => ['cssValue' => 'radial-gradient(circle,#cbd5e1 1.5px,transparent 1.5px)',   'bgSize' => '24px 24px', 'bgColor' => '#f8fafc'],
        'wg_grid'     => ['cssValue' => 'linear-gradient(#e2e8f0 1px,transparent 1px),linear-gradient(90deg,#e2e8f0 1px,transparent 1px)', 'bgSize' => '24px 24px', 'bgColor' => '#f8fafc'],
        'wg_diagonal' => ['cssValue' => 'repeating-linear-gradient(45deg,#cbd5e1,#cbd5e1 1px,transparent 1px,transparent 12px)', 'bgColor' => '#f1f5f9'],
        'wg_checker'  => ['cssValue' => 'conic-gradient(#e2e8f0 90deg,#f8fafc 90deg 180deg,#e2e8f0 180deg 270deg,#f8fafc 270deg)', 'bgSize' => '20px 20px', 'bgColor' => '#f8fafc'],
        'wg_dotsdark' => ['cssValue' => 'radial-gradient(circle,#475569 1.5px,transparent 1.5px)',   'bgSize' => '24px 24px', 'bgColor' => '#1e293b'],
        'wg_griddark' => ['cssValue' => 'linear-gradient(#334155 1px,transparent 1px),linear-gradient(90deg,#334155 1px,transparent 1px)', 'bgSize' => '24px 24px', 'bgColor' => '#0f172a'],
        'wg_wave'     => ['cssValue' => 'repeating-radial-gradient(circle at 0 0,transparent 0,#e0f2fe 8px),repeating-linear-gradient(#bae6fd55,#bae6fd)'],
        'wg_mesh'     => ['cssValue' => 'radial-gradient(at 40% 20%,#fde68a 0,transparent 50%),radial-gradient(at 80% 0,#c7d2fe 0,transparent 50%),radial-gradient(at 0 50%,#fecdd3 0,transparent 50%)', 'bgColor' => '#fff7ed'],
        'wg_white'    => ['cssValue' => '#ffffff'],
        'wg_cream'    => ['cssValue' => '#fef9f0'],
        'wg_blush'    => ['cssValue' => '#fdf2f8'],
        'wg_mint'     => ['cssValue' => '#f0fdf4'],
        'wg_sky'      => ['cssValue' => '#f0f9ff'],
        'wg_gray'     => ['cssValue' => '#f1f5f9'],
        'wg_warmgray' => ['cssValue' => '#fafaf9'],
        'wg_sand'     => ['cssValue' => '#fef3c7'],
        'wg_obsidian' => ['cssValue' => '#0a0a0a'],
        'wg_night'    => ['cssValue' => '#0f172a'],
        'wg_smoke'    => ['cssValue' => '#1c1c1e'],
        'wg_deep'     => ['cssValue' => '#111827'],
        'wg_void'     => ['cssValue' => 'linear-gradient(135deg,#0f0c29,#302b63,#24243e)'],
        'wg_abyss'    => ['cssValue' => 'linear-gradient(135deg,#000000,#434343)'],
        'wg_cosmos'   => ['cssValue' => 'linear-gradient(135deg,#0d0d0d,#1a1a2e,#16213e)'],
        'wg_eclipse'  => ['cssValue' => 'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)'],
    ];

    // ═══════════════════════════════════════════
    // HELPER METHODS — dipanggil AppearanceController
    // ═══════════════════════════════════════════

    /**
     * CSS value untuk background body (inline style / broadcast payload)
     */
    public function getBackgroundCss(): string
    {
        $bgType = $this->bg_type ?? 'color';

        // Wallpaper galeri
        if ($bgType === 'image' && $this->bg_image && str_starts_with($this->bg_image, 'wg_')) {
            $wg = self::$wallpaperMap[$this->bg_image] ?? null;
            return $wg ? $wg['cssValue'] : ($this->background_color ?? '#ffffff');
        }

        // Gambar upload sendiri
        if ($bgType === 'image' && $this->bg_image) {
            return "url('" . asset('storage/' . $this->bg_image) . "') center/cover no-repeat fixed";
        }

        // Gradien
        if ($bgType === 'gradient' && $this->bg_gradient_start && $this->bg_gradient_end) {
            $dir = $this->bg_gradient_direction ?? 'to bottom';
            return "linear-gradient({$dir}, {$this->bg_gradient_start}, {$this->bg_gradient_end})";
        }

        // Warna solid
        return $this->background_color ?? '#ffffff';
    }

    /**
     * background-color tambahan untuk pattern wallpaper (CSS property terpisah)
     */
    public function getBackgroundColor(): ?string
    {
        if (($this->bg_type ?? '') === 'image' && $this->bg_image && str_starts_with($this->bg_image, 'wg_')) {
            return self::$wallpaperMap[$this->bg_image]['bgColor'] ?? null;
        }
        return null;
    }

    /**
     * background-size tambahan untuk pattern wallpaper (CSS property terpisah)
     */
    public function getBackgroundSize(): ?string
    {
        if (($this->bg_type ?? '') === 'image' && $this->bg_image && str_starts_with($this->bg_image, 'wg_')) {
            return self::$wallpaperMap[$this->bg_image]['bgSize'] ?? null;
        }
        return null;
    }

    /**
     * CSS class untuk button shape (legacy, tetap dipertahankan)
     */
    public function getButtonShapeClass(): string
    {
        return match ($this->btn_shape) {
            'pill'   => 'border-radius: 50px',
            'square' => 'border-radius: 4px',
            default  => 'border-radius: 12px',
        };
    }
}
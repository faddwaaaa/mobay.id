<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $profile = $user->profile;

        // ── Background CSS dari DB ──
        $bgCss        = '#f9fafb';
        $bgColorExtra = null;
        $bgSizeExtra  = null;
        if ($profile) {
            if ($profile->bg_type === 'image' && $profile->bg_image && str_starts_with($profile->bg_image, 'wg_')) {
                $wgData = [
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
                    'wg_dots'     => ['cssValue' => 'radial-gradient(circle,#cbd5e1 1.5px,transparent 1.5px)', 'bgSize' => '24px 24px', 'bgColor' => '#f8fafc'],
                    'wg_grid'     => ['cssValue' => 'linear-gradient(#e2e8f0 1px,transparent 1px),linear-gradient(90deg,#e2e8f0 1px,transparent 1px)', 'bgSize' => '24px 24px', 'bgColor' => '#f8fafc'],
                    'wg_diagonal' => ['cssValue' => 'repeating-linear-gradient(45deg,#cbd5e1,#cbd5e1 1px,transparent 1px,transparent 12px)', 'bgColor' => '#f1f5f9'],
                    'wg_checker'  => ['cssValue' => 'conic-gradient(#e2e8f0 90deg,#f8fafc 90deg 180deg,#e2e8f0 180deg 270deg,#f8fafc 270deg)', 'bgSize' => '20px 20px', 'bgColor' => '#f8fafc'],
                    'wg_dotsdark' => ['cssValue' => 'radial-gradient(circle,#475569 1.5px,transparent 1.5px)', 'bgSize' => '24px 24px', 'bgColor' => '#1e293b'],
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
                $wg = $wgData[$profile->bg_image] ?? null;
                if ($wg) {
                    $bgCss        = $wg['cssValue'];
                    $bgColorExtra = $wg['bgColor'] ?? null;
                    $bgSizeExtra  = $wg['bgSize']  ?? null;
                }
            } elseif ($profile->bg_type === 'gradient' && $profile->bg_gradient_start && $profile->bg_gradient_end) {
                $dir   = $profile->bg_gradient_direction ?? 'to bottom';
                $bgCss = "linear-gradient({$dir}, {$profile->bg_gradient_start}, {$profile->bg_gradient_end})";
            } elseif ($profile->bg_type === 'image' && $profile->bg_image) {
                $bgCss = "url('" . asset('storage/' . $profile->bg_image) . "') center/cover no-repeat fixed";
            } elseif ($profile->background_color) {
                $bgCss = $profile->background_color;
            }
        }

        // ── Font, warna teks, tombol ──
        $fontFamily  = $profile->font_family   ?? 'system-ui';
        $textColor   = $profile->text_color    ?? '#111827';
        $btnColor    = $profile->btn_color     ?? '#3b82f6';
        $btnTxtColor = $profile->btn_text_color ?? '#ffffff';
        $btnShape    = $profile->btn_shape     ?? 'rounded';
        $btnStyle    = $profile->btn_style     ?? 'fill';
        $shapeMap    = ['pill' => '50px', 'rounded' => '12px', 'square' => '4px'];
        $btnRadius   = $shapeMap[$btnShape] ?? '12px';
        $toRgba      = static function (?string $color, float $alpha, string $fallback = '59,130,246'): string {
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
        };

        switch ($btnStyle) {
            case 'outline':
                $btnCssDb = "background:transparent; color:{$btnColor}; border:2px solid {$btnColor};";
                break;
            case 'hard_shadow':
                $btnCssDb = "background:{$btnColor}; color:{$btnTxtColor}; border:2px solid #111; box-shadow:3px 3px 0 #111;";
                break;
            case 'soft_shadow':
                $btnCssDb = "background:{$btnColor}; color:{$btnTxtColor}; border:none; box-shadow:0 4px 16px rgba(0,0,0,0.15);";
                break;
            case 'ghost':
                $btnCssDb = "background:rgba(255,255,255,0.15); color:{$btnTxtColor}; border:1.5px solid rgba(255,255,255,0.3); backdrop-filter:blur(8px);";
                break;
            case 'minimal':
                $btnCssDb = "background:transparent; color:{$btnColor}; border:none; border-bottom:2px solid {$btnColor}; border-radius:0 !important;";
                break;
            case 'neon':
                $btnGlowColor = $profile->btn_glow_color ?? '#38bdf8';
                $btnGlowBg = $profile->btn_glow_bg ?? '#111827';
                $btnCssDb = "background:{$btnGlowBg}; color:{$btnTxtColor}; border:2px solid {$btnGlowColor}; box-shadow:0 0 12px ".$toRgba($btnGlowColor, 0.45).", 0 0 24px ".$toRgba($btnGlowColor, 0.25).";";
                break;
            case 'glass':
                $btnCssDb = "background:".$toRgba($btnColor, 0.12)."; color:{$btnTxtColor}; border:1px solid ".$toRgba($btnColor, 0.28)."; box-shadow:0 8px 20px ".$toRgba($btnColor, 0.14)."; backdrop-filter:blur(6px);";
                break;
            default:
                $btnCssDb = "background:{$btnColor}; color:{$btnTxtColor}; border:2px solid {$btnColor};";
        }
    @endphp

    @if($fontFamily !== 'system-ui')
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontFamily) }}:wght@400;500;600;700&display=swap" rel="stylesheet">
    @endif

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── Iframe preview: fixed 375px, no scrollbar ── */
        html, body {
            width: 375px;
            max-width: 375px;
            overflow-x: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        html::-webkit-scrollbar, body::-webkit-scrollbar { display: none; }

        body {
            font-family: '{{ $fontFamily }}', system-ui, -apple-system, sans-serif;
            background: {{ $bgCss }};
            --btn-color: {{ $btnColor }};
            --btn-text-color: {{ $btnTxtColor }};
            @if($bgColorExtra) background-color: {{ $bgColorExtra }}; @endif
            @if($bgSizeExtra)  background-size: {{ $bgSizeExtra }}; @endif
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .page-wrapper {
            width: 375px;
            max-width: 375px;
            background: transparent;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            scrollbar-width: none;
        }
        .page-wrapper::-webkit-scrollbar { display: none; }

        /* Fixed elements pakai 375px */
        .product-detail-overlay,
        .search-results-overlay {
            width: 375px !important;
            left: 0 !important;
            right: auto !important;
            top: 0 !important;
            bottom: 0 !important;
            max-width: 375px !important;
        }
        .search-results-panel {
            left: 0 !important; right: 0 !important;
            width: 375px !important; max-width: 375px !important;
        }
        .product-detail-overlay {
            display: none;
            justify-content: center !important;
            align-items: center !important;
            padding: 0 20px !important;
            box-sizing: border-box !important;
        }
        .product-detail-box {
            width: 100% !important; max-width: 100% !important;
            margin: 0 !important; position: relative !important;
            left: 0 !important; right: 0 !important;
            transform: none !important;
        }

        .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: #111827; color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: 500; z-index: 9999; opacity: 0; transition: all 0.35s cubic-bezier(.34,1.56,.64,1); white-space: nowrap; pointer-events: none; }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.success { background: #16a34a; }
        .toast.error   { background: #dc2626; }

        /* ── Navbar ── */
        .navbar { background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(229,231,235,0.8); position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .navbar-container { padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-left  { display: flex; align-items: center; gap: 12px; }
        .navbar-title { font-size: 16px; font-weight: 600; color: #111827; }
        .navbar-right { display: flex; gap: 8px; }
        .nav-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: background 0.2s; position: relative; }
        .nav-icon:hover { background: #f3f4f6; }
        .nav-icon.report { color: #be123c; }
        .nav-icon.report:hover { background: #fff1f2; }
        .cart-badge { position: absolute; top: 2px; right: 2px; background: #ef4444; color: white; font-size: 10px; font-weight: 600; width: 16px; height: 16px; border-radius: 50%; display: none; align-items: center; justify-content: center; }
        .cart-badge.visible { display: flex; }
        .hamburger { width: 34px; height: 34px; display: grid; grid-template-columns: repeat(3, 4px); grid-template-rows: repeat(3, 4px); gap: 4px; place-content: center; cursor: pointer; border-radius: 8px; }
        .hamburger:active { transform: scale(0.93); }
        .hamburger span { width: 4px; height: 4px; background: #374151; border-radius: 50%; display: block; }

        /* ── Search ── */
        .search-bar-wrap { position: sticky; top: 61px; z-index: 99; background: rgba(255,255,255,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid #e5e7eb; max-height: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s ease; }
        .search-bar-wrap.open { max-height: 72px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .search-bar-inner { padding: 12px 16px; display: flex; align-items: center; gap: 8px; }
        .search-back-btn { width: 34px; height: 34px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none; background: none; cursor: pointer; border-radius: 8px; color: #6b7280; transition: background 0.2s; }
        .search-back-btn:hover { background: #f3f4f6; }
        .search-input-wrap { flex: 1; position: relative; }
        .search-input { width: 100%; height: 40px; padding: 0 38px 0 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #111827; background: #f9fafb; outline: none; transition: border-color 0.2s, background 0.2s; -webkit-appearance: none; appearance: none; }
        .search-input:focus { border-color: #2563eb; background: #fff; }
        .search-input::placeholder { color: #9ca3af; }
        .search-clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; border-radius: 50%; background: #d1d5db; color: #fff; border: none; cursor: pointer; display: none; align-items: center; justify-content: center; padding: 0; }
        .search-clear-btn svg { width: 10px; height: 10px; }
        .search-clear-btn.visible { display: flex; }
        .search-results-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.35); backdrop-filter: blur(2px); z-index: 98; opacity: 0; visibility: hidden; transition: opacity 0.25s, visibility 0.25s; }
        .search-results-overlay.active { opacity: 1; visibility: visible; }
        .search-results-panel { position: absolute; top: 133px; left: 0; right: 0; max-height: calc(100vh - 153px); background: #fff; border-radius: 0 0 16px 16px; overflow-y: auto; z-index: 99; box-shadow: 0 8px 32px rgba(0,0,0,0.12); padding-bottom: 12px; opacity: 0; transform: translateY(-8px); transition: opacity 0.25s, transform 0.25s; pointer-events: none; }
        .search-results-panel.active { opacity: 1; transform: translateY(0); pointer-events: auto; }
        .search-result-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f9fafb; }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f9fafb; }
        .search-result-thumb { width: 46px; height: 46px; border-radius: 10px; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .search-result-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .thumb-product { background: #eff6ff; color: #2563eb; } .thumb-link { background: #f0fdf4; color: #16a34a; } .thumb-text { background: #fffbeb; color: #d97706; } .thumb-other { background: #f3f4f6; color: #6b7280; }
        .search-result-info { flex: 1; min-width: 0; }
        .search-result-title { font-size: 14px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .search-result-title mark { background: #dbeafe; color: #1d4ed8; border-radius: 2px; padding: 0 1px; font-weight: 700; }
        .search-result-meta  { font-size: 12px; color: #6b7280; }
        .search-result-price { font-size: 13px; font-weight: 700; color: #2563eb; }
        .search-result-type-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.4px; flex-shrink: 0; }
        .badge-product { background: #dbeafe; color: #2563eb; } .badge-link { background: #dcfce7; color: #16a34a; } .badge-text { background: #fef9c3; color: #a16207; } .badge-other { background: #f3f4f6; color: #6b7280; }
        .search-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; gap: 8px; color: #9ca3af; text-align: center; }
        .search-state p { font-size: 13px; } .search-state strong { display: block; font-size: 15px; color: #374151; margin-bottom: 4px; }
        .search-section-label { font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: 0.8px; text-transform: uppercase; padding: 10px 16px 4px; }

        /* ── Full Menu ── */
        .fullmenu-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); z-index: 500; opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s ease; display: flex; flex-direction: column; align-items: center; }
        .fullmenu-overlay.active { opacity: 1; visibility: visible; }
        .fullmenu-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow-y: auto; width: 100%; padding: 60px 20px 40px; }
        .fullmenu-close-wrap { width: 100%; max-width: 320px; position: absolute; top: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: flex-start; }
        .fullmenu-close { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.1); border: none; color: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .fullmenu-close:hover { background: #ff0000; }
        .fullmenu-section-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.35); letter-spacing: 1px; text-transform: uppercase; text-align: center; margin-bottom: 8px; margin-top: 24px; width: 100%; max-width: 320px; }
        .fullmenu-section-label:first-child { margin-top: 0; }
        .fullmenu-item { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 14px 32px; color: rgba(255,255,255,0.85); cursor: pointer; border-radius: 12px; font-size: 17px; font-weight: 500; width: 100%; max-width: 320px; text-align: center; transform: translateY(10px); opacity: 0; transition: transform 0.25s ease, opacity 0.25s ease, background 0.15s, color 0.15s; }
        .fullmenu-overlay.active .fullmenu-item { transform: translateY(0); opacity: 1; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(1) { transition-delay: 0.05s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(2) { transition-delay: 0.10s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(3) { transition-delay: 0.15s; }
        .fullmenu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .fullmenu-item.active { color: #fff; background: rgba(59,130,246,0.2); }
        .fullmenu-item svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.7; }
        .fullmenu-item.active svg { opacity: 1; }
        .fullmenu-divider { height: 1px; background: rgba(255,255,255,0.08); width: 100%; max-width: 320px; margin: 12px 0; }

        /* ── Layout ── */
        .container { padding: 24px 16px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* ── Banner full width ── */
        .profile-banner {
            width: 100%;
            height: 180px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin-bottom: 16px;
             border-radius: 0 0 35px 35px;
        }

        /* ── Profile Card ── */
        .user-profile { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid rgba(229,231,235,0.6); }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: #e5e7eb; margin: 0 auto 10px; display: block; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb; position: relative; z-index: 1; }
        .avatar-placeholder { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.3); backdrop-filter: blur(4px); margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: #9ca3af; border: 3px solid #fff; box-shadow: 0 0 0 2px rgba(229,231,235,0.5); position: relative; z-index: 1; }

        .profile-name     { font-size: 17px; font-weight: 700; color: {{ $textColor }}; margin-bottom: 2px; }
        .profile-username { font-size: 13px; color: {{ $textColor }}; opacity: 0.65; margin-bottom: 8px; }
        .profile-bio      { font-size: 13px; color: {{ $textColor }}; opacity: 0.8; line-height: 1.5; margin-bottom: 12px; }

        .social-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 14px; }
        .social-link { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); backdrop-filter: blur(6px); border: 1.5px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.18s; color: {{ $textColor }}; flex-shrink: 0; }
        .social-link:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .social-link svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── Blocks ── */
        .block { margin-bottom: 12px; }
        .block-text { font-size: 14px; text-align: center; color: #374151; line-height: 1.6; }
        .block-link a {
            display: block; padding: 14px;
            border-radius: {{ $btnRadius }};
            text-align: center; text-decoration: none; font-weight: 500;
            transition: filter 0.2s, transform 0.2s;
            {{ $btnCssDb }}
        }
        .block-link a:hover { filter: brightness(0.93); transform: translateY(-1px); }
        .block-image img    { width: 100%; border-radius: 12px; }
        .block-video iframe { width: 100%; height: 200px; border-radius: 12px; border: none; }

        /* ── Product Skeleton ── */
        .product-skeleton { background: rgba(255,255,255,0.85); border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
        .skeleton-img  { width: 100%; height: 200px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
        .skeleton-body { padding: 14px 16px; }
        .skeleton-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; margin-bottom: 8px; }
        .skeleton-line.w60 { width: 60%; } .skeleton-line.w40 { width: 40%; }
        @keyframes shimmer { to { background-position: -200% 0; } }

        /* ── Product Block ── */
        .block-product { background: rgba(255,255,255,0.9); backdrop-filter: blur(8px); border: 1px solid rgba(229,231,235,0.7); border-radius: 12px; overflow: hidden; transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; cursor: pointer; }
        .block-product:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.1); transform: translateY(-2px); border-color: #bfdbfe; }

        .blocks-container { display: flex; flex-direction: column; gap: 12px; }

        /* ── GRID ── */
        .blocks-container.layout-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; width: 100%; }
        .blocks-container.layout-grid .block-text, .blocks-container.layout-grid .block-link, .blocks-container.layout-grid .block-image, .blocks-container.layout-grid .block-video { grid-column: 1 / -1; }
        .blocks-container.layout-grid .block-product { width: 100%; border-radius: 14px; overflow: hidden; background: #fff; display: flex; flex-direction: column; }
        .blocks-container.layout-grid .block-product .product-image-wrapper { width: 100%; aspect-ratio: 1 / 1; overflow: hidden; }
        .blocks-container.layout-grid .block-product .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .blocks-container.layout-grid .block-product .product-details { padding: 8px; }
        .blocks-container.layout-grid .block-product .product-title { font-size: 12px; font-weight: 600; margin-bottom: 4px; line-height: 1.2; }
        .blocks-container.layout-grid .block-product .product-current-price { font-size: 13px; font-weight: 700; white-space: nowrap; }
        .blocks-container.layout-grid .block-product .product-original-price { font-size: 10px; white-space: nowrap; opacity: 0.7; }
        .blocks-container.layout-grid .block-product .product-discount-badge { display: none; }

        /* ── DEFAULT ── */
        .blocks-container.layout-default { display: flex; flex-direction: column; gap: 12px; }
        .blocks-container.layout-default .block-product { display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; }
        .blocks-container.layout-default .block-product .product-image-wrapper { width: 100%; height: 200px; flex-shrink: 0; }
        .blocks-container.layout-default .block-product .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .blocks-container.layout-default .block-product .product-details { padding: 14px 16px 16px; }
        .blocks-container.layout-default .block-product .product-title { font-size: 15px; font-weight: 600; margin-bottom: 10px; line-height: 1.4; }
        .blocks-container.layout-default .block-product .product-current-price { font-size: 18px; font-weight: 700; color: #2563eb; }

        /* ── COMPACT ── */
        .blocks-container.layout-compact { display: flex; flex-direction: column; gap: 10px; }
        .blocks-container.layout-compact .block-product { display: flex; flex-direction: row; align-items: center; border-radius: 14px; overflow: visible; padding: 8px 12px 8px 8px; gap: 0; min-height: 80px; }
        .blocks-container.layout-compact .block-product .product-image-wrapper { width: 64px; height: 64px; min-width: 64px; border-radius: 10px; overflow: hidden; flex-shrink: 0; background: #f3f4f6; }
        .blocks-container.layout-compact .block-product .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .blocks-container.layout-compact .block-product .product-details { flex: 1; padding: 0 0 0 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px; min-width: 0; }
        .blocks-container.layout-compact .block-product .product-badge { display: none; }
        .blocks-container.layout-compact .block-product .product-title { font-size: 13px; font-weight: 600; margin-bottom: 0; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3; }
        .blocks-container.layout-compact .block-product .product-price-section { flex-direction: column; align-items: flex-end; gap: 2px; margin-bottom: 0; flex-shrink: 0; }
        .blocks-container.layout-compact .block-product .product-current-price { font-size: 13px; font-weight: 700; color: #ffffff; background: #2563eb; padding: 4px 12px; border-radius: 50px; white-space: nowrap; line-height: 1.4; }
        .blocks-container.layout-compact .block-product .product-original-price, .blocks-container.layout-compact .block-product .product-discount-badge { display: none; }

        /* ── HIGHLIGHT ── */
        .blocks-container.layout-highlight { display: flex; flex-direction: column; gap: 10px; }
        .blocks-container.layout-highlight .block-product { display: flex; flex-direction: row; align-items: stretch; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.10); border: 1px solid rgba(0,0,0,0.06); padding: 0; position: relative; transition: box-shadow 0.2s, transform 0.2s; }
        .blocks-container.layout-highlight .block-product:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.14); transform: translateY(-2px); }
        .blocks-container.layout-highlight .block-product::before { content: ''; display: block; width: 5px; min-width: 5px; background: var(--btn-color, #2563eb); flex-shrink: 0; }
        .blocks-container.layout-highlight .block-product .product-image-wrapper { width: 76px; height: 76px; min-width: 76px; border-radius: 10px; overflow: hidden; flex-shrink: 0; align-self: center; margin: 10px 8px; background: #f3f4f6; }
        .blocks-container.layout-highlight .block-product .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .blocks-container.layout-highlight .block-product .product-details { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 12px 14px 12px 4px; gap: 5px; min-width: 0; }
        .blocks-container.layout-highlight .block-product .product-badge { display: none; }
        .blocks-container.layout-highlight .block-product .product-title { font-size: 13.5px; font-weight: 700; margin-bottom: 0; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .blocks-container.layout-highlight .block-product .product-current-price { font-size: 15px; font-weight: 700; color: #2563eb; }
        .blocks-container.layout-highlight .block-product .product-original-price { font-size: 11px; color: #9ca3af; text-decoration: line-through; }
        .blocks-container.layout-highlight .block-product .product-discount-badge { font-size: 10px; font-weight: 600; background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 4px; }

        .blocks-container.layout-masonry { column-count: 2; column-gap: 10px; }
        .blocks-container.layout-masonry > .block { display: inline-block; width: 100%; margin: 0 0 10px; break-inside: avoid; -webkit-column-break-inside: avoid; }
        .blocks-container.layout-masonry .block-product { display: flex; flex-direction: column; border-radius: 14px; overflow: hidden; }
        .blocks-container.layout-masonry .block-product .product-image-wrapper { width: 100%; aspect-ratio: 1 / 1.15; }
        .blocks-container.layout-masonry > .block:nth-child(3n) .product-image-wrapper { aspect-ratio: 1 / 1.35; }
        .blocks-container.layout-masonry > .block:nth-child(4n) .product-image-wrapper { aspect-ratio: 1 / 0.9; }
        .blocks-container.layout-masonry .block-product .product-details { padding: 10px 12px 12px; }
        .blocks-container.layout-masonry .block-product .product-title { font-size: 13px; margin-bottom: 8px; }
        .blocks-container.layout-masonry .block-product .product-price-section { gap: 6px; flex-wrap: wrap; margin-bottom: 0; }
        .blocks-container.layout-masonry .block-product .product-current-price { font-size: 14px; }
        .blocks-container.layout-masonry .block-product .product-original-price { font-size: 11px; }
        .blocks-container.layout-masonry .block-product .product-discount-badge { font-size: 9px; padding: 2px 5px; }

        .blocks-container.layout-carousel { display: flex; flex-wrap: nowrap; gap: 12px; overflow-x: auto; overflow-y: hidden; scroll-snap-type: x mandatory; padding-bottom: 6px; -webkit-overflow-scrolling: touch; }
        .blocks-container.layout-carousel > .block { flex: 0 0 82%; min-width: 82%; scroll-snap-align: start; }
        .blocks-container.layout-carousel .block-product { display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; }
        .blocks-container.layout-carousel .block-product .product-image-wrapper { width: 100%; height: 190px; }
        .blocks-container.layout-carousel .block-product .product-details { padding: 14px 16px 16px; }
        .blocks-container.layout-carousel .block-product .product-title { font-size: 15px; }

        .product-image-wrapper { width: 100%; height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .product-image-placeholder { width: 56px; height: 56px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #2563eb; }
        .product-details { padding: 14px 16px 16px; }
        .product-badge { display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; letter-spacing: 0.3px; }
        .product-title  { font-size: 15px; font-weight: 600; color: {{ $textColor }}; margin-bottom: 10px; line-height: 1.4; }
        .product-price-section { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .product-current-price  { font-size: 18px; font-weight: 700; color: #2563eb; }
        .product-original-price { font-size: 13px; color: #9ca3af; text-decoration: line-through; }
        .product-discount-badge { background: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
        .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
        .empty-icon  { width: 64px; height: 64px; background: rgba(255,255,255,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #d1d5db; }

        /* ── Product Detail Modal ── */
        .product-detail-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; justify-content: center; align-items: center; z-index: 9999; padding: 20px; }
        .product-detail-box { position: relative; background: white; width: 100%; max-width: 420px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15); display: flex; flex-direction: column; max-height: 90vh; }
        .product-detail-close { position: absolute; top: 12px; left: 12px; z-index: 10; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); border: none; border-radius: 50%; width: 35px; height: 35px; font-weight: bold; cursor: pointer; font-size: 14px; }
        .product-detail-image  { width: 100%; height: 220px; background: #f3f4f6; flex-shrink: 0; }
        .product-detail-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-detail-content { padding: 20px; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; }
        .product-detail-content h2 { font-size: 20px; font-weight: 700; }
        .detail-price   { display: flex; align-items: center; gap: 10px; }
        .final-price    { font-size: 22px; font-weight: 700; color: #2563eb; }
        .original-price { text-decoration: line-through; color: #999; font-size: 14px; }
        .discount-badge-detail { background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }
        .stock-info     { font-size: 13px; color: #555; }
        .detail-description { font-size: 14px; color: #444; line-height: 1.6; }
        .detail-buttons { display: flex; gap: 10px; padding: 16px; border-top: 1px solid #e5e7eb; background: #fff; flex-shrink: 0; }
        .btn-cart { width: 48px; height: 48px; min-width: 48px; display: flex; align-items: center; justify-content: center; border: 1px solid #2563eb; border-radius: 10px; background: #fff; font-size: 20px; cursor: pointer; transition: all 0.2s; }
        .btn-cart:hover { background: #eff6ff; } .btn-cart.loading { opacity: 0.6; pointer-events: none; }
        .btn-buy { flex: 1; padding: 12px; background: #2563eb; color: white; border-radius: 10px; font-weight: 600; font-size: 15px; border: none; cursor: pointer; transition: background 0.2s; }
        .btn-buy:hover { background: #1d4ed8; }

        /* ══════════════════════════════════════════
           CART BOTTOM SHEET
        ══════════════════════════════════════════ */
        .cart-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 500; opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .cart-overlay.active { opacity: 1; visibility: visible; }

        .cart-drawer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: #fff;
            z-index: 501;
            border-radius: 20px 20px 0 0;
            transition: transform 0.38s cubic-bezier(.32,1.1,.64,1);
            display: flex;
            flex-direction: column;
            max-height: 88vh;
            overflow: hidden;
            box-shadow: 0 -8px 40px rgba(0,0,0,0.18);
            transform: translateY(100%);
        }
        .cart-drawer.active {
            transform: translateY(0);
        }

        .cart-handle {
            width: 40px; height: 4px;
            background: #e5e7eb; border-radius: 99px;
            margin: 10px auto 0;
            flex-shrink: 0;
            cursor: grab;
        }

        .cart-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 20px 14px;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
        }
        .cart-header h3 { font-size: 16px; font-weight: 700; color: #111827; }
        .cart-close {
            width: 32px; height: 32px; border-radius: 50%;
            background: #f3f4f6; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: #6b7280;
        }
        .cart-close:hover { background: #e5e7eb; }

        /* Select all row */
        .cart-select-all-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
            background: #fafafa;
        }
        .cart-select-all-row label { font-size: 12px; font-weight: 600; color: #6b7280; cursor: pointer; user-select: none; }
        .cart-select-all-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer; }

        .cart-items { flex: 1; overflow-y: auto; padding: 4px 20px 8px; }

        .cart-item {
            display: flex; gap: 12px;
            padding: 12px 0; border-bottom: 1px solid #f3f4f6;
            animation: fadeInUp 0.2s ease; align-items: center;
        }
        .cart-item:last-child { border-bottom: none; }

        .cart-item-check { flex-shrink: 0; width: 20px; height: 20px; accent-color: #2563eb; cursor: pointer; }

        .cart-item-img { width: 60px; height: 60px; border-radius: 10px; background: #f3f4f6; flex-shrink: 0; overflow: hidden; }
        .cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-title { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
        .cart-item-price { font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 6px; }
        .qty-control { display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .qty-btn { width: 28px; height: 28px; border: none; background: #f9fafb; cursor: pointer; font-size: 15px; font-weight: 600; display: flex; align-items: center; justify-content: center; transition: background 0.2s; color: #374151; }
        .qty-btn:hover { background: #e5e7eb; } .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .qty-value { min-width: 32px; text-align: center; font-size: 13px; font-weight: 600; padding: 0 4px; color: #111827; background: #fff; }
        .cart-item-remove { align-self: flex-start; margin-top: 2px; flex-shrink: 0; width: 26px; height: 26px; border-radius: 6px; background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; transition: all 0.2s; }
        .cart-item-remove:hover { background: #fee2e2; color: #dc2626; }

        .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px; gap: 12px; color: #9ca3af; }
        .cart-empty p { font-size: 14px; }

        /* Order Summary */
        .cart-order-summary { padding: 12px 20px 0; border-top: 1px solid #f3f4f6; flex-shrink: 0; }
        .cart-summary-label-sm { font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: .6px; text-transform: uppercase; margin-bottom: 8px; }
        .cart-summary-row { display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; margin-bottom: 4px; }
        .cart-summary-row.grand { font-size: 15px; font-weight: 700; color: #111827; margin-top: 6px; padding-top: 8px; border-top: 1px dashed #e5e7eb; }

        .cart-footer {
            padding: 14px 20px calc(24px + env(safe-area-inset-bottom, 0px));
            border-top: 1px solid #f3f4f6;
            flex-shrink: 0;
            background: #fff;
        }
        .cart-footer-hint { font-size: 11px; color: #9ca3af; text-align: center; margin-bottom: 8px; min-height: 16px; }
        .btn-checkout { width: 100%; padding: 14px; background: #2563eb; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:hover { background: #1d4ed8; } .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-continue-shopping {
            width: 100%; padding: 11px; background: transparent; color: #2563eb;
            border: 1.5px solid #2563eb; border-radius: 12px; font-size: 14px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
            margin-top: 8px; margin-bottom: 0; display: block;
        }
        .btn-continue-shopping:hover { background: #eff6ff; }

        .cart-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: #6b7280; font-size: 14px; gap: 8px; }

        /* Checkout Picker */
        .checkout-picker-overlay { position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.55); display: flex; align-items: flex-end; justify-content: center; }
        .checkout-picker-sheet { background: #fff; width: 100%; max-width: 375px; border-radius: 20px 20px 0 0; padding: 20px 20px calc(20px + env(safe-area-inset-bottom, 0px)); max-height: 70vh; overflow-y: auto; box-shadow: 0 -8px 40px rgba(0,0,0,0.18); animation: slideUpSheet 0.32s cubic-bezier(.32,1.1,.64,1); }
        @keyframes slideUpSheet { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .checkout-picker-handle { width: 40px; height: 4px; background: #e5e7eb; border-radius: 99px; margin: 0 auto 16px; }
        .checkout-picker-title { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .checkout-picker-sub { font-size: 12px; color: #6b7280; margin-bottom: 16px; }
        .checkout-picker-item { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1.5px solid #e5e7eb; border-radius: 12px; margin-bottom: 8px; cursor: pointer; transition: all 0.15s; background: #fff; }
        .checkout-picker-item:hover { border-color: #2563eb; background: #eff6ff; }
        .checkout-picker-img { width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #f3f4f6; flex-shrink: 0; }
        .checkout-picker-img img { width: 100%; height: 100%; object-fit: cover; }
        .checkout-picker-img-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .checkout-picker-info { flex: 1; min-width: 0; }
        .checkout-picker-name { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .checkout-picker-price { font-size: 13px; font-weight: 700; color: #2563eb; margin-top: 2px; }
        .checkout-picker-arrow { color: #d1d5db; flex-shrink: 0; }
        .checkout-picker-cancel { width: 100%; padding: 12px; background: #f3f4f6; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; color: #374151; cursor: pointer; margin-top: 4px; transition: background 0.2s; }
        .checkout-picker-cancel:hover { background: #e5e7eb; }
        /* ══════════════════════════════════════════ */

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { width: 18px; height: 18px; border: 2px solid #e5e7eb; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="toast" id="toast"></div>

<div class="page-wrapper" id="pageWrapper">

{{-- NAVBAR --}}
<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon report" id="reportBtn" title="Laporkan">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v18m0-11h11l-2 3 2 3H5"/></svg>
            </div>
            <div class="nav-icon" id="searchBtn" title="Cari">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            </div>
            <div class="nav-icon" id="cartBtn" title="Keranjang">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="cart-badge" id="cartBadge">0</span>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="search-bar-wrap" id="searchBarWrap">
    <div class="search-bar-inner">
        <button class="search-back-btn" id="searchBackBtn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="search-input-wrap">
            <input type="text" class="search-input" id="searchInput" placeholder="Cari produk, link, konten..." autocomplete="off">
            <button class="search-clear-btn" id="searchClearBtn">
                <svg viewBox="0 0 10 10" fill="currentColor"><path d="M6.41 5l2.3-2.29a1 1 0 00-1.42-1.42L5 3.59 2.71 1.29A1 1 0 001.29 2.71L3.59 5 1.29 7.29a1 1 0 001.42 1.42L5 6.41l2.29 2.3a1 1 0 001.42-1.42z"/></svg>
            </button>
        </div>
    </div>
</div>
<div class="search-results-overlay" id="searchResultsOverlay"></div>
<div class="search-results-panel"  id="searchResultsPanel"></div>

{{-- FULLSCREEN MENU --}}
<div class="fullmenu-overlay" id="fullmenuOverlay">
    <div class="fullmenu-close-wrap">
        <button class="fullmenu-close" id="fullmenuClose">&#10005;</button>
    </div>
    <div class="fullmenu-body">
        @if($user->pages && $user->pages->count() > 0)
            <div class="fullmenu-section-label">Halaman</div>
            @foreach($user->pages as $userPage)
                <div class="fullmenu-item {{ $loop->first ? 'active' : '' }}" data-tab="page-{{ $userPage->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $userPage->title }}
                </div>
            @endforeach
        @endif
        <div class="fullmenu-divider"></div>
        <div class="fullmenu-section-label">Ruang Pengguna</div>
        <div class="fullmenu-item" onclick="window.location.href='/login'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Masuk / Daftar
        </div>
    </div>
</div>

{{-- BANNER --}}
@if($profile && $profile->banner_image)
<div class="profile-banner"
     style="background-image: url('{{ asset('storage/' . $profile->banner_image) }}')">
</div>
@endif

{{-- KONTEN --}}
<div class="container">
    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="tab-page-{{ $userPage->id }}">

                {{-- Profile Card --}}
                <div class="user-profile">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar" alt="{{ $user->name }}">
                    @else
                        <div class="avatar-placeholder">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif

                    <div class="profile-name" data-profile-text>{{ $user->name }}</div>
                    <div class="profile-username" data-profile-text>{{ '@' . $user->username }}</div>

                    @if($profile && $profile->about)
                        <div class="profile-bio" data-profile-text>{{ $profile->about }}</div>
                    @elseif($user->bio ?? false)
                        <div class="profile-bio" data-profile-text>{{ $user->bio }}</div>
                    @endif

                    @if(!empty($socialLinks))
                    @php
                    $socialSvgMap = [
                        'telegram'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.93 6.686l-1.683 7.927c-.127.567-.46.707-.931.44l-2.57-1.894-1.24 1.193c-.137.137-.252.252-.517.252l.185-2.621 4.768-4.307c.207-.185-.045-.287-.322-.102L7.89 14.214l-2.522-.788c-.548-.171-.558-.548.115-.812l9.867-3.805c.456-.166.856.112.58.877z"/></svg>',
                        'website'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
                        'email'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                        'discord'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>',
                        'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.27 8.27 0 0 0 4.84 1.55V6.79a4.85 4.85 0 0 1-1.07-.1z"/></svg>',
                        'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
                        'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>',
                        'twitch'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>',
                        'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                        'x'         => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.732-8.858L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                        'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                        'behance'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 7h-7V5h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14H15.97c.13 3.211 3.483 3.312 4.588 2.029H23.726zm-7.726-3h3.457c-.073-1.580-1.002-2.18-1.712-2.18-.747 0-1.633.572-1.745 2.18zM7.17 9.025c.395 0 2.353.105 2.353 1.734 0 .97-.771 1.463-1.55 1.546v.047c.99.078 1.968.609 1.968 1.873 0 2.006-2.006 2.072-2.637 2.072H1V9.025h6.17zm-3.07 5.52h2.167c.588 0 1.14-.228 1.14-.91 0-.773-.693-.9-1.244-.9H4.1v1.81zm0-3.31h1.937c.5 0 1.057-.162 1.057-.836 0-.73-.625-.836-1.14-.836H4.1v1.672z"/></svg>',
                        'dribbble'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 24C5.385 24 0 18.615 0 12S5.385 0 12 0s12 5.385 12 12-5.385 12-12 12zm10.12-10.358c-.35-.11-3.17-.953-6.384-.438 1.34 3.684 1.887 6.684 1.992 7.308 2.3-1.555 3.936-4.02 4.395-6.87zm-6.115 7.808c-.153-.9-.75-4.032-2.19-7.77l-.066.02c-5.79 2.015-7.86 6.017-8.04 6.39 1.73 1.35 3.92 2.166 6.29 2.166 1.42 0 2.77-.29 4.01-.806zm-9.86-3.28c.24-.38 3.28-5.21 8.536-6.89.016-.064.033-.128.05-.192-1.52-.547-4.73-1.07-8.52-1.07-.284 0-.568.004-.85.012-.04.166-.065.334-.065.504 0 3.126 1.19 5.99 3.14 8.13zm7.715-10.27c-.47-1.353-1.31-3.373-2.38-5.13-1.34.09-2.63.41-3.79.94 1.46 1.764 2.546 3.764 2.77 4.43.67-.12 1.39-.2 2.16-.2.42 0 .83.02 1.24.06zm.36-.09c.46.03.92.09 1.37.17.01-.04.01-.09.01-.13 0-1.72-.468-3.335-1.286-4.72-.29.75-.784 2.52-1.094 4.68zm3.327.55c-.34-.066-.69-.12-1.043-.157.16-1.766.566-3.457 1.05-4.656.66.39 1.25.87 1.78 1.404-.676.952-1.452 2.307-1.787 3.41z"/></svg>',
                        'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>',
                        'spotify'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>',
                        'threads'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.689-2.046 1.367-1.455 2.041-3.534 2.075-6.154H12.79v-2.113h9.23c.16 3.404-.499 6.094-1.97 8.009-1.855 2.364-4.797 3.6-8.868 3.623z"/></svg>',
                    ];
                    @endphp
                    <div class="social-links">
                        @foreach($socialLinks as $platform => $url)
                            @if($url)
                            <a href="{{ $url }}"
                               target="{{ $platform === 'email' ? '_self' : '_blank' }}"
                               rel="noopener noreferrer"
                               class="social-link"
                               title="{{ ucfirst($platform) }}">
                                {!! $socialSvgMap[$platform] ?? '<span style="font-size:11px;font-weight:700;">'.strtoupper(substr($platform,0,2)).'</span>' !!}
                            </a>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Blocks --}}
                @php
                    $allowedLayouts = ['default', 'grid', 'compact', 'highlight'];
                    $blockLayout = in_array($profile->block_layout ?? 'default', $allowedLayouts, true)
                        ? ($profile->block_layout ?? 'default')
                        : 'default';
                @endphp
                @if($userPage->blocks && $userPage->blocks->count() > 0)
                    <div class="blocks-container layout-{{ $blockLayout }}" id="blocksContainer">
                    @foreach($userPage->blocks->sortBy('position') as $block)
                        @if($block->type === 'text')
                            <div class="block block-text" id="block-{{ $block->id }}">{{ $block->content['text'] ?? '' }}</div>
                        @elseif($block->type === 'link')
                            <div class="block block-link" id="block-{{ $block->id }}">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">{{ $block->content['title'] ?? 'Link' }}</a>
                            </div>
                        @elseif($block->type === 'image')
                            <div class="block block-image" id="block-{{ $block->id }}">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>
                        @elseif($block->type === 'video')
                            @php
                                $videoId = $block->content['youtube_id'] ?? '';
                                if (!$videoId) {
                                    $url = $block->content['youtube_url'] ?? '';
                                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                                    $videoId = $query['v'] ?? '';
                                    if (!$videoId && str_contains($url, 'youtu.be/'))
                                        $videoId = basename(parse_url($url, PHP_URL_PATH));
                                }
                            @endphp
                            <div class="block block-video" id="block-{{ $block->id }}">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                            </div>
                        @elseif($block->type === 'product' && isset($block->product_id) && $block->product_id)
                            <div class="block" id="product-block-{{ $block->id }}" data-product-id="{{ $block->product_id }}">
                                <div class="product-skeleton">
                                    <div class="skeleton-img"></div>
                                    <div class="skeleton-body">
                                        <div class="skeleton-line"></div>
                                        <div class="skeleton-line w60"></div>
                                        <div class="skeleton-line w40"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        Halaman ini belum memiliki konten.
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            Belum ada halaman.
        </div>
    @endif
</div>

{{-- PRODUCT DETAIL MODAL --}}
<div class="product-detail-overlay" id="productDetailModal">
    <div class="product-detail-box">
        <button class="product-detail-close" onclick="closeProductDetail()">&#10005;</button>
        <div class="product-detail-image"><img id="detailImage" src="" alt="" style="display:none;"></div>
        <div class="product-detail-content">
            <h2 id="detailTitle"></h2>
            <div class="detail-price">
                <span class="final-price" id="detailFinalPrice"></span>
                <span class="original-price" id="detailOriginalPrice"></span>
                <span class="discount-badge-detail" id="detailDiscountBadge" style="display:none;"></span>
            </div>
            <div class="stock-info" id="detailStockWrap">Stok: <span id="detailStock"></span></div>
            <div class="detail-description">
                <strong style="font-size:13px;color:#6b7280;letter-spacing:.5px;">DESKRIPSI</strong>
                <p id="detailDescription" style="margin-top:6px;"></p>
            </div>
        </div>
        <div class="detail-buttons">
            <button class="btn-cart" id="btnAddToCart">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </button>
            <button class="btn-buy" id="buyNowBtn">Beli Sekarang</button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     CART BOTTOM SHEET
     ════════════════════════════════════════ --}}
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
    {{-- Handle bar --}}
    <div class="cart-handle" id="cartHandle"></div>

    {{-- Header --}}
    <div class="cart-header">
        <h3 id="cartTitle">Keranjang Belanja</h3>
        <button class="cart-close" onclick="closeCart()">&#10005;</button>
    </div>

    {{-- Select All --}}
    <div class="cart-select-all-row" id="cartSelectAllRow" style="display:none;">
        <input type="checkbox" id="cartCheckAll" onchange="toggleSelectAll(this.checked)">
        <label for="cartCheckAll" id="cartSelectAllLabel">Pilih Semua</label>
    </div>

    {{-- Items --}}
    <div class="cart-items" id="cartItems">
        <div class="cart-loading"><div class="spinner"></div> Memuat...</div>
    </div>

    {{-- Summary + Footer --}}
    <div id="cartSummarySection" style="display:none;">
        <div class="cart-order-summary">
            <div class="cart-summary-label-sm">Ringkasan Pesanan</div>
            <div class="cart-summary-row">
                <span id="cartSelectedLabel">0 item dipilih</span>
                <span id="cartSubtotal">Rp 0</span>
            </div>
            <div class="cart-summary-row grand">
                <span>Grand Total</span>
                <span id="cartTotal">Rp 0</span>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-footer-hint" id="cartHint"></div>
            <button class="btn-checkout" id="btnCheckout" onclick="handleCheckout()" disabled>
                Beli Sekarang
            </button>
            <button class="btn-continue-shopping" onclick="closeCart()">Lanjut Belanja</button>
        </div>
    </div>
</div>
{{-- ════════════════════ END CART ════════════════════ --}}

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
function formatRupiah(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }
let toastTimer;
function showToast(msg, type = 'default') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}
async function apiCall(url, method = 'GET', body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
    return data;
}
function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div'); d.textContent = str; return d.innerHTML;
}
const SEARCH_ICONS = {
    product: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`,
    link:    `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`,
    text:    `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    other:   `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h8m-8 4h4"/></svg>`,
};
const THUMB_CLASS = { product: 'thumb-product', link: 'thumb-link', text: 'thumb-text', other: 'thumb-other' };
const PRODUCT_PLACEHOLDER_SVG = `<svg width="28" height="28" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CART_PLACEHOLDER_SVG    = `<svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CART_EMPTY_SVG          = `<svg width="40" height="40" fill="none" stroke="#d1d5db" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`;

// ── Menu ──
const hamburger = document.getElementById('hamburger');
const fullmenuOverlay = document.getElementById('fullmenuOverlay');
const fullmenuClose   = document.getElementById('fullmenuClose');
let scrollY = 0;
function openMenu()  { scrollY = window.scrollY; document.body.style.position='fixed'; document.body.style.top=`-${scrollY}px`; document.body.style.width='100%'; fullmenuOverlay.classList.add('active'); }
function closeMenu() { fullmenuOverlay.classList.remove('active'); document.body.style.position=''; document.body.style.top=''; document.body.style.width=''; window.scrollTo(0, scrollY); }
hamburger.addEventListener('click', openMenu);
fullmenuClose.addEventListener('click', closeMenu);
document.querySelectorAll('.fullmenu-item[data-tab]').forEach(item => {
    item.addEventListener('click', () => {
        const tab = item.dataset.tab;
        document.querySelectorAll('.fullmenu-item').forEach(n => n.classList.remove('active'));
        item.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(`tab-${tab}`)?.classList.add('active');
        closeMenu();
    });
});

// ── Products ──
function renderProductBlock(container, product) {
    const price = parseFloat(product.price)||0, discount = parseFloat(product.discount)||0;
    const finalPrice = parseFloat(product.final_price)||((discount>0&&discount<price)?discount:price);
    const hasDis = finalPrice < price, discPct = hasDis ? Math.round(((price-finalPrice)/price)*100) : 0;
    const imgHtml = product.image_url ? `<img src="${product.image_url}" alt="${escHtml(product.title)}">` : `<div class="product-image-placeholder">${PRODUCT_PLACEHOLDER_SVG}</div>`;
    const badgeHtml = hasDis ? `<span class="product-badge">Diskon ${discPct}%</span>` : '';
    const priceHtml = hasDis ? `<div class="product-current-price">${formatRupiah(finalPrice)}</div><div class="product-original-price">${formatRupiah(price)}</div><div class="product-discount-badge">-${discPct}%</div>` : `<div class="product-current-price">${formatRupiah(finalPrice)}</div>`;
    container.innerHTML = `<div class="block-product" onclick="handleProductClick(${product.id})"><div class="product-image-wrapper">${imgHtml}</div><div class="product-details">${badgeHtml}<div class="product-title">${escHtml(product.title)}</div><div class="product-price-section">${priceHtml}</div></div></div>`;
}

document.addEventListener('DOMContentLoaded', function () {
    fetch(`/api/profile/{{ $user->username }}/view`, { method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'} }).catch(()=>{});
    const productContainers = document.querySelectorAll('[data-product-id]');
    if (productContainers.length > 0) {
        const ids = [...new Set([...productContainers].map(el => el.getAttribute('data-product-id')))].join(',');
        fetch(`/api/products/batch?ids=${ids}`).then(r=>r.json()).then(productsMap => {
            productContainers.forEach(container => {
                const pid = container.getAttribute('data-product-id');
                const product = productsMap[pid];
                if (product) renderProductBlock(container, product);
                else container.innerHTML = `<div style="background:rgba(255,255,255,0.85);border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`;
            });
        }).catch(() => {
            productContainers.forEach(container => {
                const pid = container.getAttribute('data-product-id');
                fetch(`/api/product/${pid}/data`).then(r=>r.json()).then(p=>renderProductBlock(container,p)).catch(()=>{
                    container.innerHTML = `<div style="background:rgba(255,255,255,0.85);border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`;
                });
            });
        });
    }
    apiCall('/api/cart').then(d => updateBadge(d.total_items)).catch(()=>{});
});

function updateBadge(count) {
    const badge = document.getElementById('cartBadge');
    badge.textContent = count;
    badge.classList.toggle('visible', count > 0);
}

// ── Product Detail ──
let currentProductId = null;
function handleProductClick(productId) {
    if (!productId) return;
    fetch(`/api/product/${productId}/view`, { method:'POST', headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json'} }).catch(()=>{});
    fetch(`/api/product/${productId}/data`).then(r=>r.json()).then(product => {
        currentProductId = product.id;
        const price = parseFloat(product.price)||0, discount = parseFloat(product.discount)||0;
        const finalPrice = parseFloat(product.final_price)||((discount>0&&discount<price)?discount:price);
        const hasDis = finalPrice < price, discPct = hasDis ? Math.round(((price-finalPrice)/price)*100) : 0;
        document.getElementById('detailTitle').textContent = product.title;
        document.getElementById('detailFinalPrice').textContent = formatRupiah(finalPrice);
        document.getElementById('detailOriginalPrice').textContent = hasDis ? formatRupiah(price) : '';
        const discBadge = document.getElementById('detailDiscountBadge');
        if (hasDis) { discBadge.textContent=`-${discPct}%`; discBadge.style.display='inline-block'; } else discBadge.style.display='none';
        const stockWrap = document.getElementById('detailStockWrap');
        if (product.product_type==='digital'||product.stock===null) stockWrap.style.display='none';
        else { stockWrap.style.display=''; document.getElementById('detailStock').textContent=product.stock; }
        document.getElementById('detailDescription').textContent = product.description??'';
        const img = document.getElementById('detailImage');
        if (product.image_url) { img.src=product.image_url; img.style.display='block'; } else img.style.display='none';
        document.getElementById('buyNowBtn').onclick = () => { window.location.href=`/checkout/${product.id}`; };
        document.getElementById('productDetailModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }).catch(()=>showToast('Gagal memuat produk.','error'));
}
function closeProductDetail() { document.getElementById('productDetailModal').style.display='none'; document.body.style.overflow='auto'; currentProductId=null; }
document.getElementById('btnAddToCart').addEventListener('click', async () => {
    if (!currentProductId) return;
    const btn = document.getElementById('btnAddToCart');
    btn.classList.add('loading');
    try { const data = await apiCall('/api/cart/add','POST',{product_id:currentProductId,quantity:1}); updateBadge(data.total_items); showToast('Produk ditambahkan ke keranjang!','success'); }
    catch (err) { showToast(err.message,'error'); }
    finally { btn.classList.remove('loading'); }
});

// ══════════════════════════════════════════
// CART BOTTOM SHEET — dengan pilih produk
// ══════════════════════════════════════════
const cartOverlay = document.getElementById('cartOverlay');
const cartDrawer  = document.getElementById('cartDrawer');

// Swipe down to close
let cartTouchStartY = 0;
document.getElementById('cartHandle').addEventListener('touchstart', e => {
    cartTouchStartY = e.touches[0].clientY;
}, { passive: true });
document.getElementById('cartHandle').addEventListener('touchmove', e => {
    if (e.touches[0].clientY - cartTouchStartY > 60) closeCart();
}, { passive: true });

function openCart() {
    cartOverlay.classList.add('active');
    cartDrawer.classList.add('active');
    document.body.style.overflow = 'hidden';
    loadCart();
}
function closeCart() {
    cartOverlay.classList.remove('active');
    cartDrawer.classList.remove('active');
    document.body.style.overflow = '';
}
cartOverlay.addEventListener('click', closeCart);
document.getElementById('cartBtn').addEventListener('click', openCart);

// State: Set of selected cart item IDs
let selectedCartItems = new Set();
// Cache data items terakhir
let _lastCartData = null;

async function loadCart() {
    const container = document.getElementById('cartItems');
    const summary   = document.getElementById('cartSummarySection');
    const selectAllRow = document.getElementById('cartSelectAllRow');
    container.innerHTML = `<div class="cart-loading"><div class="spinner"></div> Memuat...</div>`;
    summary.style.display = 'none';
    selectAllRow.style.display = 'none';
    try {
        const data = await apiCall('/api/cart');
        renderCartItems(data);
    } catch {
        container.innerHTML = `<div class="cart-empty">${CART_EMPTY_SVG}<p>Gagal memuat keranjang.</p></div>`;
    }
}

function renderCartItems(data) {
    const container    = document.getElementById('cartItems');
    const summary      = document.getElementById('cartSummarySection');
    const titleEl      = document.getElementById('cartTitle');
    const selectAllRow = document.getElementById('cartSelectAllRow');

    if (!data.items || data.items.length === 0) {
        container.innerHTML = `<div class="cart-empty">${CART_EMPTY_SVG}<p>Keranjangmu masih kosong.</p></div>`;
        summary.style.display = 'none';
        selectAllRow.style.display = 'none';
        titleEl.textContent = 'Keranjang Belanja';
        selectedCartItems = new Set();
        _lastCartData = null;
        return;
    }

    _lastCartData = data.items;
    titleEl.textContent = `Keranjang (${data.items.length})`;

    const existingIds = new Set(data.items.map(i => i.id));
    for (const id of selectedCartItems) {
        if (!existingIds.has(id)) selectedCartItems.delete(id);
    }
    if (selectedCartItems.size === 0) {
        data.items.forEach(i => selectedCartItems.add(i.id));
    }

    container.innerHTML = data.items.map(item => {
        const checked = selectedCartItems.has(item.id) ? 'checked' : '';
        return `
        <div class="cart-item" id="cart-item-${item.id}">
            <input type="checkbox" class="cart-item-check" id="chk-${item.id}"
                ${checked}
                onchange="onItemCheckChange(${item.id}, this.checked)">
            <div class="cart-item-img">
                ${item.image_url
                    ? `<img src="${item.image_url}" alt="${escHtml(item.title)}">`
                    : `<div class="cart-item-img-placeholder">${CART_PLACEHOLDER_SVG}</div>`}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-title">${escHtml(item.title)}</div>
                <div class="cart-item-price">
                    ${formatRupiah(item.final_price)}
                    ${item.has_discount
                        ? `<span style="font-size:11px;color:#9ca3af;text-decoration:line-through;margin-left:4px;">${formatRupiah(item.original_price)}</span>`
                        : ''}
                </div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${item.id},${item.quantity - 1},${item.stock})" ${item.quantity <= 1 ? 'disabled' : ''}>&#8722;</button>
                    <span class="qty-value">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${item.id},${item.quantity + 1},${item.stock})" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                </div>
            </div>
            <button class="cart-item-remove" onclick="removeItem(${item.id})">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>`;
    }).join('');

    selectAllRow.style.display = 'flex';
    summary.style.display = 'block';
    refreshSummary();
}

function onItemCheckChange(id, checked) {
    if (checked) selectedCartItems.add(id);
    else selectedCartItems.delete(id);
    refreshSummary();
}

function toggleSelectAll(checked) {
    if (!_lastCartData) return;
    if (checked) {
        _lastCartData.forEach(i => selectedCartItems.add(i.id));
    } else {
        selectedCartItems.clear();
    }
    _lastCartData.forEach(i => {
        const chk = document.getElementById(`chk-${i.id}`);
        if (chk) chk.checked = checked;
    });
    refreshSummary();
}

function refreshSummary() {
    if (!_lastCartData) return;

    const total = _lastCartData.length;
    const selectedItems = _lastCartData.filter(i => selectedCartItems.has(i.id));
    const count = selectedItems.length;
    const grandTotal = selectedItems.reduce((sum, i) => sum + (i.final_price * i.quantity), 0);

    const checkAllEl = document.getElementById('cartCheckAll');
    const selectLabelEl = document.getElementById('cartSelectAllLabel');
    if (checkAllEl) {
        checkAllEl.checked = count === total && total > 0;
        checkAllEl.indeterminate = count > 0 && count < total;
    }
    if (selectLabelEl) {
        selectLabelEl.textContent = count === total
            ? 'Pilih Semua'
            : `Pilih Semua (${count}/${total} dipilih)`;
    }

    document.getElementById('cartSelectedLabel').textContent = `${count} item dipilih`;
    document.getElementById('cartSubtotal').textContent  = formatRupiah(grandTotal);
    document.getElementById('cartTotal').textContent     = formatRupiah(grandTotal);

    const hintEl      = document.getElementById('cartHint');
    const checkoutBtn = document.getElementById('btnCheckout');

    if (count === 0) {
        hintEl.textContent        = 'Centang produk yang ingin dibeli';
        checkoutBtn.disabled      = true;
        checkoutBtn.textContent   = 'Beli Sekarang';
    } else if (count === 1) {
        hintEl.textContent        = '';
        checkoutBtn.disabled      = false;
        checkoutBtn.textContent   = 'Beli Sekarang';
    } else {
        hintEl.textContent        = `${count} produk dipilih — pilih salah satu untuk checkout`;
        checkoutBtn.disabled      = false;
        checkoutBtn.textContent   = `Checkout (${count} produk)`;
    }
}

async function changeQty(id, newQty, stock) {
    if (newQty < 1 || newQty > stock) return;
    try {
        const data = await apiCall(`/api/cart/${id}`, 'PATCH', { quantity: newQty });
        updateBadge(data.total_items);
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) { showToast(err.message, 'error'); }
}

async function removeItem(id) {
    const itemEl = document.getElementById(`cart-item-${id}`);
    if (itemEl) { itemEl.style.opacity = '0.4'; itemEl.style.transition = 'opacity 0.2s'; }
    selectedCartItems.delete(id);
    try {
        const data = await apiCall(`/api/cart/${id}`, 'DELETE');
        updateBadge(data.total_items);
        showToast('Produk dihapus dari keranjang.');
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) {
        showToast(err.message, 'error');
        if (itemEl) itemEl.style.opacity = '1';
    }
}

function handleCheckout() {
    if (!_lastCartData) return;
    const selectedItems = _lastCartData.filter(i => selectedCartItems.has(i.id));
    if (selectedItems.length === 0) {
        showToast('Pilih minimal 1 produk dulu.', 'error');
        return;
    }
    if (selectedItems.length === 1) {
        window.location.href = `/checkout/${selectedItems[0].product_id}`;
        return;
    }
    showCheckoutPicker(selectedItems);
}

function showCheckoutPicker(items) {
    const existing = document.getElementById('checkoutPickerOverlay');
    if (existing) existing.remove();

    const itemsHtml = items.map(item => `
        <div class="checkout-picker-item" onclick="window.location.href='/checkout/${item.product_id}'">
            <div class="checkout-picker-img">
                ${item.image_url
                    ? `<img src="${item.image_url}" alt="${escHtml(item.title)}">`
                    : `<div class="checkout-picker-img-ph">${CART_PLACEHOLDER_SVG}</div>`}
            </div>
            <div class="checkout-picker-info">
                <div class="checkout-picker-name">${escHtml(item.title)}</div>
                <div class="checkout-picker-price">${formatRupiah(item.final_price)}</div>
            </div>
            <div class="checkout-picker-arrow">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>`).join('');

    const pickerHtml = `
    <div id="checkoutPickerOverlay" class="checkout-picker-overlay"
         onclick="if(event.target===this){ document.getElementById('checkoutPickerOverlay').remove(); }">
        <div class="checkout-picker-sheet">
            <div class="checkout-picker-handle"></div>
            <div class="checkout-picker-title">Pilih Produk untuk Checkout</div>
            <div class="checkout-picker-sub">Tap produk yang ingin kamu beli sekarang</div>
            ${itemsHtml}
            <button class="checkout-picker-cancel"
                onclick="document.getElementById('checkoutPickerOverlay').remove()">
                Batal
            </button>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', pickerHtml);
}

// ── Search ──
const USERNAME             = '{{ $user->username }}';
const searchBtn            = document.getElementById('searchBtn');
const searchBarWrap        = document.getElementById('searchBarWrap');
const searchBackBtn        = document.getElementById('searchBackBtn');
const searchInput          = document.getElementById('searchInput');
const searchClearBtn       = document.getElementById('searchClearBtn');
const searchResultsOverlay = document.getElementById('searchResultsOverlay');
const searchResultsPanel   = document.getElementById('searchResultsPanel');
let searchDebounce = null;
function openSearch()  { searchBarWrap.classList.add('open'); setTimeout(()=>searchInput.focus(),350); }
function closeSearch() { searchBarWrap.classList.remove('open'); hideResults(); searchInput.value=''; searchClearBtn.classList.remove('visible'); }
function showResults() { searchResultsOverlay.classList.add('active'); searchResultsPanel.classList.add('active'); }
function hideResults() { searchResultsOverlay.classList.remove('active'); searchResultsPanel.classList.remove('active'); }
searchBtn.addEventListener('click', openSearch);
searchBackBtn.addEventListener('click', closeSearch);
searchResultsOverlay.addEventListener('click', closeSearch);
searchClearBtn.addEventListener('click', ()=>{ searchInput.value=''; searchClearBtn.classList.remove('visible'); hideResults(); searchInput.focus(); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape'&&searchBarWrap.classList.contains('open')) closeSearch(); });
function highlight(text,query) {
    if(!query||!text) return escHtml(text);
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
    return escHtml(text).replace(new RegExp(escaped,'gi'),m=>`<mark>${m}</mark>`);
}
function renderState(stateType,title,sub) {
    const icons = {
        loading:`<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24" style="animation:spin .8s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>`,
        noresult:`<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>`,
        error:`<svg width="28" height="28" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
    };
    searchResultsPanel.innerHTML = `<div class="search-state"><div class="search-state-icon">${icons[stateType]||icons.noresult}</div><strong>${escHtml(title)}</strong>${sub?`<p>${escHtml(sub)}</p>`:''}</div>`;
}
function normalizeItem(raw) {
    const type = raw.type||'other'; const title = raw.title||raw.url||'(tanpa judul)';
    return { id:raw.id??null, type, title, subtitle:raw.subtitle??null, image_url:raw.image_url??null, url:raw.url??null, price:raw.price??0, final_price:raw.final_price??raw.price??0, block_id:raw.block_id??raw.id??null };
}
function renderResults(results,query) {
    if (!Array.isArray(results)||!results.length) { renderState('noresult','Tidak ditemukan',`Tidak ada hasil untuk "${query}"`); return; }
    const labelMap = {product:'Produk',link:'Link',text:'Konten',other:'Lainnya'};
    const groups   = {product:[],link:[],text:[],other:[]};
    results.map(normalizeItem).forEach(item=>{ const key=groups[item.type]!==undefined?item.type:'other'; groups[key].push(item); });
    let html='';
    for (const [type,items] of Object.entries(groups)) {
        if (!items.length) continue;
        html+=`<div class="search-section-label">${labelMap[type]||'Lainnya'}</div>`;
        items.forEach(item=>{
            const thumbClass=THUMB_CLASS[type]||THUMB_CLASS.other;
            const thumbInner=item.image_url?`<img src="${item.image_url}" alt="">`:SEARCH_ICONS[type]||SEARCH_ICONS.other;
            const badge=`<span class="search-result-type-badge badge-${type in groups?type:'other'}">${labelMap[type]||'Lainnya'}</span>`;
            const subHtml=type==='product'?`<div class="search-result-price">${formatRupiah(item.final_price)}</div>`:(item.subtitle?`<div class="search-result-meta">${escHtml(String(item.subtitle))}</div>`:'');
            const itemJson=JSON.stringify(item).replace(/</g,'\\u003c').replace(/>/g,'\\u003e').replace(/&/g,'\\u0026');
            html+=`<div class="search-result-item" onclick='handleSearchResultClick(${itemJson})'><div class="search-result-thumb ${item.image_url?'':thumbClass}">${thumbInner}</div><div class="search-result-info"><div class="search-result-title">${highlight(String(item.title),query)}</div>${subHtml}</div>${badge}</div>`;
        });
    }
    searchResultsPanel.innerHTML = html;
}
function handleSearchResultClick(item) {
    if (item.type==='product') { closeSearch(); handleProductClick(item.id); }
    else if (item.type==='link') { window.open(item.url,'_blank'); }
    else {
        closeSearch();
        const blockEl = document.getElementById(`block-${item.block_id}`);
        if (blockEl) {
            const tabContent = blockEl.closest('.tab-content');
            if (tabContent&&!tabContent.classList.contains('active')) {
                document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
                tabContent.classList.add('active');
                const tabId = tabContent.id.replace('tab-','');
                document.querySelectorAll('.fullmenu-item[data-tab]').forEach(n=>{ n.classList.toggle('active',n.dataset.tab===tabId); });
            }
            setTimeout(()=>blockEl.scrollIntoView({behavior:'smooth',block:'center'}),100);
        }
    }
}
searchInput.addEventListener('input', ()=>{
    const q=searchInput.value.trim();
    searchClearBtn.classList.toggle('visible',q.length>0);
    clearTimeout(searchDebounce);
    if (!q) { hideResults(); return; }
    showResults(); renderState('loading','Mencari...','');
    searchDebounce=setTimeout(()=>doSearch(q),350);
});
async function doSearch(query) {
    try {
        const res = await fetch(`/search?username=${encodeURIComponent(USERNAME)}&q=${encodeURIComponent(query)}`,{headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken}});
        const contentType = res.headers.get('content-type')||'';
        if (!contentType.includes('application/json')) { renderState('error','Route tidak ditemukan',`Status ${res.status}`); return; }
        const json = await res.json();
        let results = Array.isArray(json)?json:(json.results??json.data??json.items??[]);
        if (!results.length&&typeof json==='object') { for (const val of Object.values(json)) { if (Array.isArray(val)&&val.length>0) { results=val; break; } } }
        renderResults(results,query);
    } catch(err) { renderState('error','Gagal mencari',err?.message||'Terjadi kesalahan'); }
}

// ── Live preview via postMessage dari appearance editor ──
(function () {
    function applyAppearance(p) {
        if (!p) return;
        if (p.bgImage) {
            document.body.style.backgroundImage    = `url('${p.bgImage}')`;
            document.body.style.backgroundSize     = 'cover';
            document.body.style.backgroundPosition = 'center';
            document.body.style.backgroundRepeat   = 'no-repeat';
            document.body.style.backgroundColor   = '';
            document.body.style.backgroundAttachment = 'fixed';
        } else if (p.bgCss) {
            const isSolid = /^#[0-9a-fA-F]{3,8}$|^rgb/.test(p.bgCss.trim());
            if (isSolid) {
                document.body.style.backgroundImage = 'none';
                document.body.style.backgroundColor = p.bgCss;
            } else {
                document.body.style.backgroundImage = p.bgCss;
                document.body.style.backgroundColor = p.bgColor ?? '';
                document.body.style.backgroundSize  = p.bgSize  ?? 'cover';
            }
            document.body.style.backgroundPosition   = 'center';
            document.body.style.backgroundRepeat     = 'no-repeat';
            document.body.style.backgroundAttachment = 'fixed';
        }
        if (p.fontFamily) {
            document.body.style.fontFamily = `'${p.fontFamily}', system-ui, -apple-system, sans-serif`;
            injectFont(p.fontFamily);
        }
        if (p.textColor) {
            document.querySelectorAll('[data-profile-text]').forEach(el => { el.style.color = p.textColor; });
            document.querySelectorAll('.social-link, .block-text, .product-title').forEach(el => { el.style.color = p.textColor; });
        }
        if (p.btnCss || p.btnRadius) {
            document.querySelectorAll('.block-link a, .btn-buy, .btn-checkout, .btn-cart').forEach(btn => applyBtnStyle(btn, p));
        }
        if (p.block_layout) {
            const container = document.getElementById('blocksContainer');
            if (container) {
                container.className = container.className.replace(/\blayout-\S+/g, '').trim();
                container.classList.add('layout-' + p.block_layout);
            }
        }
    }

    function applyBtnStyle(btn, p) {
        btn.style.background = ''; btn.style.backgroundColor = ''; btn.style.color = '';
        btn.style.border = ''; btn.style.borderBottom = ''; btn.style.borderRadius = '';
        btn.style.boxShadow = ''; btn.style.backdropFilter = '';
        if (p.btnRadius && p.btn_style !== 'minimal') btn.style.borderRadius = p.btnRadius;
        if (!p.btnCss) return;
        p.btnCss.split(';').filter(s => s.trim()).forEach(pair => {
            const idx = pair.indexOf(':'); if (idx === -1) return;
            const prop = pair.slice(0, idx).trim(); const val = pair.slice(idx + 1).trim();
            if (!prop || !val) return;
            const camel = prop.replace(/-([a-z])/g, (_, c) => c.toUpperCase());
            try { btn.style[camel] = val; } catch(e) {}
        });
        if (p.btn_text_color) btn.style.color = p.btn_text_color;
    }

    function injectFont(fontName) {
        const id = 'gf-' + fontName.replace(/\s+/g, '-').toLowerCase();
        if (document.getElementById(id)) return;
        const link = document.createElement('link');
        link.id = id; link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(fontName)}:wght@400;500;600;700&display=swap`;
        document.head.appendChild(link);
    }

    window.addEventListener('message', function (e) {
        if (!e.data || e.data.type !== 'payou_appearance_update') return;
        applyAppearance(e.data.payload);
    });

    if (typeof BroadcastChannel !== 'undefined') {
        const bc = new BroadcastChannel('payou_appearance');
        bc.onmessage = function (e) {
            if (e.data && e.data.type === 'payou_appearance_saved') applyAppearance(e.data.payload);
        };
    }
})();
</script>
</div>
@include('components.app-alert')
</body>
</html>

<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class TrackClickMetadata
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (session()->has('click_id')) {
            $agent = new Agent();

            DB::table('payou_id_clicks')
                ->where('id', session('click_id'))
                ->update([
                    'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isDesktop() ? 'desktop' : 'tablet'),
                    'referrer_source' => parse_url(url()->previous(), PHP_URL_HOST),
                ]);
        }

        return $response;
    }
}

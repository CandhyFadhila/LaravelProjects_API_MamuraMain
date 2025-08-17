<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SiteViewCounter;
use Illuminate\Support\Str;

class CountSiteVisit
{
    public function handle(Request $request, Closure $next)
    {
        // hitung hanya request GET/HEAD agar POST/AJAX aksi tidak dihitung
        if (in_array($request->method(), ['GET', 'HEAD'], true)) {
            $secPurpose = strtolower((string) $request->headers->get('sec-purpose'));
            if ($secPurpose !== 'prefetch') {
                app(SiteViewCounter::class)->count($request);
            }
        }

        return $next($request);
    }
}

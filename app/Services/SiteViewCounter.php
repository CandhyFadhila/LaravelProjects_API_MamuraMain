<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteViewCounter
{
  /**
   * Hitung 1x/visitor per HARI untuk seluruh situs (IP + UA pendek).
   */
  public function count(Request $request): void
  {
    // 0) Skip bot/prefetch
    $ua = strtolower((string) $request->userAgent());
    $secPurpose = strtolower((string) $request->headers->get('sec-purpose'));
    if (
      $ua === '' || str_contains($ua, 'bot') || str_contains($ua, 'crawler') || str_contains($ua, 'spider')
      || str_contains($ua, 'preview') || str_contains($ua, 'fetch') || str_contains($secPurpose, 'prefetch')
    ) {
      return;
    }

    // 1) IP (pastikan Trusted Proxies sudah benar)
    $ip = $request->ip();
    if (!$ip) return;

    // 2) Tanggal sesuai TZ app
    $tz = config('app.timezone', 'Asia/Jakarta');
    $visitDate = CarbonImmutable::now($tz)->toDateString();

    // 3) Hash harian (IP + UA short + tanggal) dengan kunci app
    $uaShort = substr($ua, 0, 32);
    $ipHash = hash_hmac('sha256', $ip . '|' . $uaShort . '|' . $visitDate, (string) config('app.key'));

    // Dedupe: insert sekali per hari; kalau duplikat → diabaikan
    DB::table('site_view_ip_uniques')->insertOrIgnore([
      'ip_hash'     => $ipHash,
      'visit_date'  => $visitDate,
      'created_at'  => now(),
      'updated_at'  => now(),
    ]);
  }
}

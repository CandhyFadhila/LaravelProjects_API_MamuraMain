<?php

namespace App\Services;

use App\Models\Blog;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogViewCounter
{
  public function count(Blog $blog, Request $request): void
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
    $dateWib = CarbonImmutable::now($tz)->toDateString();

    // 3) Hash harian (IP + UA short + tanggal) dengan kunci app
    $uaShort = substr($ua, 0, 32);
    $ipHash = hash_hmac('sha256', $ip . '|' . $uaShort . '|' . $dateWib, (string) config('app.key'));

    // 4) Insert-or-ignore dedupe → increment jika sukses
    $inserted = DB::table('blog_view_ip_uniques')->insertOrIgnore([
      'blog_id'   => $blog->id,
      'ip_hash'   => $ipHash,
      'view_date' => $dateWib,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    if ($inserted) {
      DB::table('blogs')->where('id', $blog->id)->increment('views');
    }
  }
}

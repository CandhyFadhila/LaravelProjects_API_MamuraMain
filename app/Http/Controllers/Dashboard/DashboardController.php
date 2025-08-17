<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Templates\WithDataResource;
use App\Http\Resources\Templates\WithoutDataResource;
use App\Models\Carrier;
use App\Models\JobApplication;
use App\Models\SupportedCity;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function countSiteViews()
    {
        try {
            if (!Gate::allows('masterdata.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $tz         = config('app.timezone', 'Asia/Jakarta');
            $now        = now($tz);

            // Periode sekarang
            $today      = $now->toDateString();
            $weekStart  = $now->copy()->startOfWeek()->toDateString();
            $weekEnd    = $now->copy()->endOfWeek()->toDateString();
            $monthStart = $now->copy()->startOfMonth()->toDateString();
            $monthEnd   = $now->copy()->endOfMonth()->toDateString();
            $yearStart  = $now->copy()->startOfYear()->toDateString();
            $yearEnd    = $now->copy()->endOfYear()->toDateString();

            // Periode pembanding
            $yesterday       = $now->copy()->subDay()->toDateString();
            $lastWeekStart   = $now->copy()->subWeek()->startOfWeek()->toDateString();
            $lastWeekEnd     = $now->copy()->subWeek()->endOfWeek()->toDateString();
            $lastMonthStart  = $now->copy()->subMonth()->startOfMonth()->toDateString();
            $lastMonthEnd    = $now->copy()->subMonth()->endOfMonth()->toDateString();
            $lastYearStart   = $now->copy()->subYear()->startOfYear()->toDateString();
            $lastYearEnd     = $now->copy()->subYear()->endOfYear()->toDateString();

            // Hitung current
            $todayCnt  = (int) DB::table('site_view_ip_uniques')->where('visit_date', $today)->count();
            $weekCnt   = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$weekStart, $weekEnd])->count();
            $monthCnt  = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$monthStart, $monthEnd])->count();
            $yearCnt   = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$yearStart, $yearEnd])->count();

            // Hitung previous
            $yesterdayCnt = (int) DB::table('site_view_ip_uniques')->where('visit_date', $yesterday)->count();
            $lastWeekCnt  = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$lastWeekStart, $lastWeekEnd])->count();
            $lastMonthCnt = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$lastMonthStart, $lastMonthEnd])->count();
            $lastYearCnt  = (int) DB::table('site_view_ip_uniques')->whereBetween('visit_date', [$lastYearStart, $lastYearEnd])->count();

            $data = [
                'today' => [
                    'count'     => $todayCnt,
                    'yesterday_count' => $yesterdayCnt,
                    'percentage_compare_yesterday' => $this->pctChangeInt($todayCnt, $yesterdayCnt),
                ],
                'this_week' => [
                    'count'     => $weekCnt,
                    'last_week_count' => $lastWeekCnt,
                    'percentage_compare_last_week' => $this->pctChangeInt($weekCnt, $lastWeekCnt),
                ],
                'this_month' => [
                    'count'     => $monthCnt,
                    'last_month_count' => $lastMonthCnt,
                    'percentage_compare_last_month' => $this->pctChangeInt($monthCnt, $lastMonthCnt),
                ],
                'this_year' => [
                    'count'     => $yearCnt,
                    'last_year_count' => $lastYearCnt,
                    'percentage_compare_last_year' => $this->pctChangeInt($yearCnt, $lastYearCnt),
                ],
            ];

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Rekap site visitor berhasil dihitung.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('dashboard')->error('| Site Views | - Error function countSiteViews : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function countBlogViews()
    {
        try {
            if (!Gate::allows('masterdata.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $tz         = config('app.timezone', 'Asia/Jakarta');
            $now        = now($tz);

            // Periode sekarang
            $today      = $now->toDateString();
            $weekStart  = $now->copy()->startOfWeek()->toDateString();
            $weekEnd    = $now->copy()->endOfWeek()->toDateString();
            $monthStart = $now->copy()->startOfMonth()->toDateString();
            $monthEnd   = $now->copy()->endOfMonth()->toDateString();
            $yearStart  = $now->copy()->startOfYear()->toDateString();
            $yearEnd    = $now->copy()->endOfYear()->toDateString();

            // Periode pembanding
            $yesterday       = $now->copy()->subDay()->toDateString();
            $lastWeekStart   = $now->copy()->subWeek()->startOfWeek()->toDateString();
            $lastWeekEnd     = $now->copy()->subWeek()->endOfWeek()->toDateString();
            $lastMonthStart  = $now->copy()->subMonth()->startOfMonth()->toDateString();
            $lastMonthEnd    = $now->copy()->subMonth()->endOfMonth()->toDateString();
            $lastYearStart   = $now->copy()->subYear()->startOfYear()->toDateString();
            $lastYearEnd     = $now->copy()->subYear()->endOfYear()->toDateString();

            // Hitung current
            $todayCnt  = (int) DB::table('blog_view_ip_uniques')->where('view_date', $today)->count();
            $weekCnt   = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$weekStart, $weekEnd])->count();
            $monthCnt  = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$monthStart, $monthEnd])->count();
            $yearCnt   = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$yearStart, $yearEnd])->count();

            // Hitung previous
            $yesterdayCnt = (int) DB::table('blog_view_ip_uniques')->where('view_date', $yesterday)->count();
            $lastWeekCnt  = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$lastWeekStart, $lastWeekEnd])->count();
            $lastMonthCnt = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$lastMonthStart, $lastMonthEnd])->count();
            $lastYearCnt  = (int) DB::table('blog_view_ip_uniques')->whereBetween('view_date', [$lastYearStart, $lastYearEnd])->count();

            $data = [
                'today' => [
                    'count'     => $todayCnt,
                    'yesterday_count' => $yesterdayCnt,
                    'percentage_compare_yesterday' => $this->pctChangeInt($todayCnt, $yesterdayCnt),
                ],
                'this_week' => [
                    'count'     => $weekCnt,
                    'last_week_count' => $lastWeekCnt,
                    'percentage_compare_last_week' => $this->pctChangeInt($weekCnt, $lastWeekCnt),
                ],
                'this_month' => [
                    'count'     => $monthCnt,
                    'last_month_count' => $lastMonthCnt,
                    'percentage_compare_last_month' => $this->pctChangeInt($monthCnt, $lastMonthCnt),
                ],
                'this_year' => [
                    'count'     => $yearCnt,
                    'last_year_count' => $lastYearCnt,
                    'percentage_compare_last_year' => $this->pctChangeInt($yearCnt, $lastYearCnt),
                ],
            ];

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Rekap blog views berhasil dihitung.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('dashboard')->error('| Blog Views | - Error function countBlogViews : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function countUserApplicationsbyCarrier()
    {
        try {
            if (!Gate::allows('masterdata.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $tz  = config('app.timezone', 'Asia/Jakarta');
            $now = Carbon::now($tz);

            // Range periode sekarang (pakai datetime agar akurat untuk created_at timestamp)
            $todayStart   = $now->copy()->startOfDay();
            $todayEnd     = $now->copy()->endOfDay();

            $weekStart    = $now->copy()->startOfWeek();
            $weekEnd      = $now->copy()->endOfWeek();

            $monthStart   = $now->copy()->startOfMonth();
            $monthEnd     = $now->copy()->endOfMonth();

            $yearStart    = $now->copy()->startOfYear();
            $yearEnd      = $now->copy()->endOfYear();

            // Range periode pembanding
            $yesterdayStart  = $now->copy()->subDay()->startOfDay();
            $yesterdayEnd    = $now->copy()->subDay()->endOfDay();

            $lastWeekStart   = $now->copy()->subWeek()->startOfWeek();
            $lastWeekEnd     = $now->copy()->subWeek()->endOfWeek();

            $lastMonthStart  = $now->copy()->subMonth()->startOfMonth();
            $lastMonthEnd    = $now->copy()->subMonth()->endOfMonth();

            $lastYearStart   = $now->copy()->subYear()->startOfYear();
            $lastYearEnd     = $now->copy()->subYear()->endOfYear();

            // ==== OVERALL (semua carrier category digabung) ====
            $todayCnt  = $this->overallCountJobApplications($todayStart, $todayEnd);
            $weekCnt   = $this->overallCountJobApplications($weekStart, $weekEnd);
            $monthCnt  = $this->overallCountJobApplications($monthStart, $monthEnd);
            $yearCnt   = $this->overallCountJobApplications($yearStart, $yearEnd);

            $yesterdayCnt = $this->overallCountJobApplications($yesterdayStart, $yesterdayEnd);
            $lastWeekCnt  = $this->overallCountJobApplications($lastWeekStart, $lastWeekEnd);
            $lastMonthCnt = $this->overallCountJobApplications($lastMonthStart, $lastMonthEnd);
            $lastYearCnt  = $this->overallCountJobApplications($lastYearStart, $lastYearEnd);

            // ==== BY CARRIER CATEGORY ====
            // Ambil baris (id, name, total) per periode, lalu ubah menjadi peta [id => total] + peta nama [id => name]
            $todayRows      = $this->groupedCountsByCategory($todayStart, $todayEnd);
            $yesterdayRows  = $this->groupedCountsByCategory($yesterdayStart, $yesterdayEnd);

            $weekRows       = $this->groupedCountsByCategory($weekStart, $weekEnd);
            $lastWeekRows   = $this->groupedCountsByCategory($lastWeekStart, $lastWeekEnd);

            $monthRows      = $this->groupedCountsByCategory($monthStart, $monthEnd);
            $lastMonthRows  = $this->groupedCountsByCategory($lastMonthStart, $lastMonthEnd);

            $yearRows       = $this->groupedCountsByCategory($yearStart, $yearEnd);
            $lastYearRows   = $this->groupedCountsByCategory($lastYearStart, $lastYearEnd);

            // Set gabungan semua category id yang muncul
            $categoryIds = collect([$todayRows, $yesterdayRows, $weekRows, $lastWeekRows, $monthRows, $lastMonthRows, $yearRows, $lastYearRows])
                ->flatMap(fn($rows) => $rows->pluck('carrier_category_id'))
                ->unique()
                ->values();

            // Peta nama kategori: id -> name (prioritaskan nama dari row manapun yang muncul)
            $nameMap = collect([$todayRows, $yesterdayRows, $weekRows, $lastWeekRows, $monthRows, $lastMonthRows, $yearRows, $lastYearRows])
                ->flatten(1)
                ->mapWithKeys(fn($r) => [$r->carrier_category_id => $r->carrier_category_name]);

            // Ubah rows menjadi peta count per periode: id -> total
            $todayMap     = $todayRows->pluck('total', 'carrier_category_id');
            $yesterdayMap = $yesterdayRows->pluck('total', 'carrier_category_id');

            $weekMap      = $weekRows->pluck('total', 'carrier_category_id');
            $lastWeekMap  = $lastWeekRows->pluck('total', 'carrier_category_id');

            $monthMap     = $monthRows->pluck('total', 'carrier_category_id');
            $lastMonthMap = $lastMonthRows->pluck('total', 'carrier_category_id');

            $yearMap      = $yearRows->pluck('total', 'carrier_category_id');
            $lastYearMap  = $lastYearRows->pluck('total', 'carrier_category_id');

            // Susun payload per kategori
            $byCarrierCategory = [];
            foreach ($categoryIds as $catId) {
                $t  = (int) ($todayMap[$catId] ?? 0);
                $yt = (int) ($yesterdayMap[$catId] ?? 0);

                $w  = (int) ($weekMap[$catId] ?? 0);
                $lw = (int) ($lastWeekMap[$catId] ?? 0);

                $m  = (int) ($monthMap[$catId] ?? 0);
                $lm = (int) ($lastMonthMap[$catId] ?? 0);

                $y  = (int) ($yearMap[$catId] ?? 0);
                $ly = (int) ($lastYearMap[$catId] ?? 0);

                $byCarrierCategory[] = [
                    'carrier_category_id'   => (int) $catId,
                    'carrier_category_name' => $nameMap->get($catId) ?? null,
                    'today' => [
                        'count' => $t,
                        'yesterday_count' => $yt,
                        'percentage_compare_yesterday' => $this->pctChangeInt($t, $yt),
                    ],
                    'this_week' => [
                        'count' => $w,
                        'last_week_count' => $lw,
                        'percentage_compare_last_week' => $this->pctChangeInt($w, $lw),
                    ],
                    'this_month' => [
                        'count' => $m,
                        'last_month_count' => $lm,
                        'percentage_compare_last_month' => $this->pctChangeInt($m, $lm),
                    ],
                    'this_year' => [
                        'count' => $y,
                        'last_year_count' => $ly,
                        'percentage_compare_last_year' => $this->pctChangeInt($y, $ly),
                    ],
                ];
            }

            $data = [
                'today' => [
                    'count' => $todayCnt,
                    'yesterday_count' => $yesterdayCnt,
                    'percentage_compare_yesterday' => $this->pctChangeInt($todayCnt, $yesterdayCnt),
                ],
                'this_week' => [
                    'count' => $weekCnt,
                    'last_week_count' => $lastWeekCnt,
                    'percentage_compare_last_week' => $this->pctChangeInt($weekCnt, $lastWeekCnt),
                ],
                'this_month' => [
                    'count' => $monthCnt,
                    'last_month_count' => $lastMonthCnt,
                    'percentage_compare_last_month' => $this->pctChangeInt($monthCnt, $lastMonthCnt),
                ],
                'this_year' => [
                    'count' => $yearCnt,
                    'last_year_count' => $lastYearCnt,
                    'percentage_compare_last_year' => $this->pctChangeInt($yearCnt, $lastYearCnt),
                ],
                'by_carrier_category' => $byCarrierCategory,
            ];

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Rekap job applications berhasil dihitung.',
                    $data
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('dashboard')->error('| Job Application | - Error function countUserApplicationsbyCarrier : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function countUserContactbyPackage()
    {
        try {
            if (!Gate::allows('masterdata.view')) {
                return response()->json(
                    new WithoutDataResource(
                        Response::HTTP_FORBIDDEN,
                        'NO_ACCESS',
                        'Tidak Memiliki Akses',
                        'Anda tidak memiliki akses untuk mengakses halaman ini.',
                    ),
                    Response::HTTP_FORBIDDEN
                );
            }

            $tz  = config('app.timezone', 'Asia/Jakarta');
            $now = now($tz);

            // Periode sekarang (pakai datetime karena kolom created_at bertipe timestamp)
            $todayStart  = $now->copy()->startOfDay();
            $todayEnd    = $now->copy()->endOfDay();

            $weekStart   = $now->copy()->startOfWeek();
            $weekEnd     = $now->copy()->endOfWeek();

            $monthStart  = $now->copy()->startOfMonth();
            $monthEnd    = $now->copy()->endOfMonth();

            $yearStart   = $now->copy()->startOfYear();
            $yearEnd     = $now->copy()->endOfYear();

            // Periode pembanding
            $yesterdayStart = $now->copy()->subDay()->startOfDay();
            $yesterdayEnd   = $now->copy()->subDay()->endOfDay();

            $lastWeekStart  = $now->copy()->subWeek()->startOfWeek();
            $lastWeekEnd    = $now->copy()->subWeek()->endOfWeek();

            $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
            $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

            $lastYearStart  = $now->copy()->subYear()->startOfYear();
            $lastYearEnd    = $now->copy()->subYear()->endOfYear();

            // Ambil semua paket agar paket tanpa inquiry tetap tampil 0
            $packages = DB::table('pricings')
                ->whereNull('deleted_at')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            // Hitung per-periode (current & previous) -> map [pricing_id => count]
            $todayMap     = $this->countsByPackage($todayStart, $todayEnd);
            $yesterdayMap = $this->countsByPackage($yesterdayStart, $yesterdayEnd);

            $weekMap      = $this->countsByPackage($weekStart, $weekEnd);
            $lastWeekMap  = $this->countsByPackage($lastWeekStart, $lastWeekEnd);

            $monthMap     = $this->countsByPackage($monthStart, $monthEnd);
            $lastMonthMap = $this->countsByPackage($lastMonthStart, $lastMonthEnd);

            $yearMap      = $this->countsByPackage($yearStart, $yearEnd);
            $lastYearMap  = $this->countsByPackage($lastYearStart, $lastYearEnd);

            // (opsional) total sepanjang waktu
            // $totalMap     = $this->countsByPackage();

            // Susun payload per paket
            $list = [];
            foreach ($packages as $p) {
                $pid = (int) $p->id;

                $t  = (int) ($todayMap[$pid] ?? 0);
                $yt = (int) ($yesterdayMap[$pid] ?? 0);

                $w  = (int) ($weekMap[$pid] ?? 0);
                $lw = (int) ($lastWeekMap[$pid] ?? 0);

                $m  = (int) ($monthMap[$pid] ?? 0);
                $lm = (int) ($lastMonthMap[$pid] ?? 0);

                $y  = (int) ($yearMap[$pid] ?? 0);
                $ly = (int) ($lastYearMap[$pid] ?? 0);

                $list[] = [
                    'pricing_id'    => $pid,
                    'pricing_name'  => $p->name,
                    'today' => [
                        'count' => $t,
                        'yesterday_count' => $yt,
                        'percentage_compare_yesterday' => $this->pctChangeInt($t, $yt),
                    ],
                    'this_week' => [
                        'count' => $w,
                        'last_week_count' => $lw,
                        'percentage_compare_last_week' => $this->pctChangeInt($w, $lw),
                    ],
                    'this_month' => [
                        'count' => $m,
                        'last_month_count' => $lm,
                        'percentage_compare_last_month' => $this->pctChangeInt($m, $lm),
                    ],
                    'this_year' => [
                        'count' => $y,
                        'last_year_count' => $ly,
                        'percentage_compare_last_year' => $this->pctChangeInt($y, $ly),
                    ],
                    // 'total_all_time' => (int) ($totalMap[$pid] ?? 0), // tanpa compare (sesuai contoh Anda)
                ];
            }

            return response()->json(
                new WithDataResource(
                    Response::HTTP_OK,
                    'SUCCESS_GET_DATA',
                    'Berhasil Mengambil Data',
                    'Rekap inquiries per paket + persentase berhasil dihitung.',
                    $list
                ),
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::channel('dashboard')->error('| Contact Package | - Error function countUserContactbyPackage : ' . $e->getMessage() . ' - Line : ' . $e->getLine());
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'ERROR_GET_DATA',
                    'Gagal Mengambil Data',
                    'Terjadi kesalahan pada sistem, silahkan coba lagi nanti atau hubungi admin.',
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Hitung perubahan persen (integer) antara current dan prev.
     * Contoh: 90 -> dibaca "90 percent".
     */
    private function pctChangeInt(int $current, int $prev): int
    {
        if ($prev === 0) return $current > 0 ? 100 : 0;
        return (int) round((($current - $prev) / $prev) * 100);
    }

    /**
     * Total semua aplikasi pada rentang waktu (exclude soft-deleted).
     */
    private function overallCountJobApplications(Carbon $start, Carbon $end): int
    {
        return (int) DB::table('job_applications as ja')
            ->whereNull('ja.deleted_at')
            ->whereBetween('ja.created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->count();
    }

    /**
     * Ambil baris agregat per carrier category di rentang waktu:
     * return Collection of rows: [{carrier_category_id, carrier_category_name, total}, ...]
     */
    private function groupedCountsByCategory(Carbon $start, Carbon $end)
    {
        return DB::table('job_applications as ja')
            ->join('carriers as c', 'c.id', '=', 'ja.carrier_id')
            ->join('carrier_categories as cc', 'cc.id', '=', 'c.carrier_category_id')
            ->whereNull('ja.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('cc.deleted_at')
            ->whereBetween('ja.created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('cc.id', 'cc.name')
            ->selectRaw('cc.id as carrier_category_id, cc.name as carrier_category_name, COUNT(*) as total')
            ->get();
    }

    /**
     * Ambil hitungan inquiries dikelompokkan per paket.
     * - Jika $start/$end null => total sepanjang waktu.
     * - Hanya menghitung inquiries dengan preferred_package_id tidak null.
     */
    private function countsByPackage(?Carbon $start = null, ?Carbon $end = null)
    {
        $q = DB::table('inquiries as iq')
            ->whereNull('iq.deleted_at')
            ->whereNotNull('iq.preferred_package_id');

        if ($start && $end) {
            $q->whereBetween('iq.created_at', [$start->toDateTimeString(), $end->toDateTimeString()]);
        }

        return $q->groupBy('iq.preferred_package_id')
            ->selectRaw('iq.preferred_package_id, COUNT(*) as total')
            ->pluck('total', 'iq.preferred_package_id'); // [pricing_id => total]
    }
}

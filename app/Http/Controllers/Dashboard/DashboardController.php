<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // TODO: Buat per hari, minggu, bulan, tahun. Dan ada 2 mode, integer dan percentage
    // jumlah pengunjung universal (yang mengunjungi landing page - per hari, minggu, bulan, tahun)
    // jumlah pengunjung rata2 per blog (per hari, minggu, bulan, tahun)
    // jumlah user yang melamar per carrier_id (tampilkan dalam masing2 carrier_id, per hari, minggu, bulan, tahun)
    // jumlah user yang meng kontak mamura untuk bertanya paket (preferred_package_id not null, per hari, minggu, bulan, tahun)
}

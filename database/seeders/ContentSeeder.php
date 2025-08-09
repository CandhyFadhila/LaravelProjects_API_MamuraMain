<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID content type berdasarkan nama
        $textTypeId = DB::table('content_types')->where('name', 'Text')->value('id');
        $tautanTypeId = DB::table('content_types')->where('name', 'Tautan')->value('id');

        $listContent = [
            "Ini content hero 1.",
            "Ini content hero 2.",
            "Ini content hero 3.",
            "Ini content hero 4.",
            "Ini content hero 5.",
            "Pilihan Tepat untuk Wifi Hemat Tanpa Drama",
            "100%",
            "Fiber Optic",
            "1:1",
            "Simetris Download:Upload",
            "100%",
            "Internet UNLIMITED",
            "INI ISINYA GAMBAR/internet.png",
            "Internetan Unlimited & Hemat? Pilihan Tepat: Mamura",
            "Super lancar, super hemat, tanpa drama. #WifiMurah terbaik untuk segala kebutuhan Anda.",
            "Cara Berlangganan Nikmati Wifi Internet Rumah di Mamura",
            "Tiga langkah mudah untuk menggunakan layanan dari kami",
            "1. Registrasi",
            "Cek area lokasi pemasangan, pilih paket, dan pilih jadwal pemasangan.",
            "2. Instalasi",
            "Lacak proses instalasi kemudian nikmati layanan MamuraNet!",
            "3. Bayar",
            "Bayar tagihan dan nikmati kecepatan layanan MamuraNet.",
            "INI ISINYA GAMBAR/about.png",
            "Komitmen Kami untuk Koneksi Tanpa Batas!!",
            "PT Mamura Inter Media adalah penyedia layanan internet lokal yang telah melayani ribuan pelanggan sejak tahun [Tahun Berdiri]. Kami percaya bahwa internet cepat tidak harus mahal. Visi kami adalah menjadi pilihan utama WiFi hemat di Indonesia dengan:",
            "Infrastruktur jaringan yang modern",
            "Tim teknisi profesional dan responsif",
            "Layanan personal yang ramah dan cepat",
            "Kami hadir untuk menjawab kebutuhan internet Anda, mulai dari keluarga kecil hingga perusahaan besar.",
            "Siap Terkoneksi dengan Kami?",
            "Kami siap bantu Anda memilih layanan terbaik sesuai kebutuhan dan anggaran.",
            "🎯 Konsultasi gratis",
            "📍 Layanan area Jabodetabek & seluruh Indonesia",
            "📞 Fast response by phone & WhatsApp",
            "https://wa.me/62895622189054",
            "INI ISINYA GAMBAR/discuss_bnw.png",
            "Frequently Ask Questions",
            "Layanan Internet Mamura",
            "https://wa.me/62895622189054",
            "Artikel Terbaru",
            "Mamura Inter Media berdiri sejak tahun 2020 telah melalui proses yang panjang dalam perjalanannya dengan fokus pada tujuan meningkatkan kualitas layanan jasa internet di Indonesia, sejalan dengan kemajuan teknologi yang terus berkembang pesat di era modern saat ini, dimana “Internet”telah menjadi bagian dari kebutuhan masyarakat umum.",
            "Jl. Raya Purwantoro - Pakis Baru KM. 08 Miri, Rt01 Rw01 Miri, Kismantoro, Wonogiri Kode Pos 57696",
            "Email info@mamura.net.id",
            "Whatsapp 0811-2539-694",
            "https://facebook.com",
            "https://instagram.com",
            "https://twitter.com",
            "https://linkedin.com",
            "Mamura Inter Media",
        ];

        $data = [];

        foreach ($listContent as $content) {
            $isLink = preg_match('/^https?:\/\//', $content);
            $typeId = $isLink ? $tautanTypeId : $textTypeId;

            $data[] = [
                'content_type_id' => $typeId,
                'content_file_id' => null,
                'content' => $content,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('contents')->insert($data);
    }
}

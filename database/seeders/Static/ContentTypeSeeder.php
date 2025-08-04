<?php

namespace Database\Seeders\Static;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Text',
                'description' => 'Konten berbasis teks seperti paragraf, kutipan, atau catatan tertulis.',
            ],
            [
                'name' => 'Gambar',
                'description' => 'Konten visual dalam bentuk gambar atau ilustrasi statis (JPEG, PNG, dll).',
            ],
            [
                'name' => 'Video',
                'description' => 'Konten multimedia berupa rekaman gambar bergerak, seperti MP4 atau format video lainnya.',
            ],
            [
                'name' => 'Audio',
                'description' => 'Konten suara seperti musik, rekaman wawancara, atau podcast (MP3, WAV, dll).',
            ],
            [
                'name' => 'File',
                'description' => 'Lampiran dokumen yang dapat diunduh seperti PDF, DOCX, atau spreadsheet.',
            ],
            [
                'name' => 'Tautan',
                'description' => 'Pranala eksternal atau internal yang mengarah ke sumber daya lain di dalam atau luar sistem.',
            ],
        ];

        foreach ($data as $item) {
            DB::table('content_types')->insert([
                'name' => $item['name'],
                'description' => $item['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

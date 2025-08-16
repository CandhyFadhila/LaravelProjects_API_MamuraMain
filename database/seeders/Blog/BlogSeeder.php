<?php

namespace Database\Seeders\Blog;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua ID kategori blog yang tersedia
        $blogCategoryIds = DB::table('blog_categories')->pluck('id')->toArray();

        $rows = [];

        for ($i = 0; $i < 20; $i++) {
            $title = $faker->unique()->sentence(6);
            $slug  = Str::slug($title);

            // Bangun konten HTML: <h2>...</h2><p>...</p>
            $sections = [];

            // Section 1: pakai judul blog sebagai <h2>
            $sections[] = sprintf(
                "<h2>%s</h2>\n<p>%s</p>",
                e($title),
                $faker->paragraph($faker->numberBetween(3, 6))
            );

            // Tambahan 2–5 section lain (total 3–6 section)
            $extraSections = $faker->numberBetween(2, 5);
            for ($s = 0; $s < $extraSections; $s++) {
                $h2 = Str::title($faker->words($faker->numberBetween(3, 7), true));
                $p  = $faker->paragraph($faker->numberBetween(3, 6));

                $sections[] = "<h2>{$h2}</h2>\n<p>{$p}</p>";
            }

            $blogContent = implode("\n\n", $sections);

            // Opsional: deskripsi bisa diambil dari paragraf pertama (plain text)
            $description = Str::limit(strip_tags($sections[0]), 180);

            $rows[] = [
                'blog_category_id' => $faker->randomElement($blogCategoryIds),
                'thumbnail_id'     => null,
                'title'            => $title,
                'slug'             => $slug,
                'description'      => $description,
                'blog_content'     => $blogContent,
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
                'deleted_at'       => null,
            ];
        }

        DB::table('blogs')->insert($rows);
    }
}

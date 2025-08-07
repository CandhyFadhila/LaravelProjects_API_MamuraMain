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

        $data = [];

        for ($i = 0; $i < 10; $i++) {
            $title = $faker->unique()->sentence(6);
            $slug = Str::slug($title);

            $data[] = [
                'blog_category_id' => $faker->randomElement($blogCategoryIds),
                'thumbnail_id' => null,
                'title' => $title,
                'slug' => $slug,
                'description' => $faker->sentence(15),
                'blog_content' => $faker->paragraphs(5, true),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('blogs')->insert($data);
    }
}

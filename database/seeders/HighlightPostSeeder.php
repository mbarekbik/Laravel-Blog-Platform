<?php

namespace Database\Seeders;

use App\Models\HighlightPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HighlightPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $postsId = [
            2,
            3,
            20
        ];

        foreach ($postsId as $postId) {
            HighlightPost::create([
                'post_id' => $postId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->words(8, true);
        $body = $this->faker->text(2000);

        $readingSpeed = 200;
        $words = str_word_count($body);
        $readingTime = ceil($words / $readingSpeed);

        return [
            'title' => $title,
            'excerpt' => $this->faker->sentence(40),
            'body' => '<p>'.$body.'</p>',
            'image_path' => $this->faker->randomElement(['/images/posts/picture2.jpg', '/images/posts/picture.jpg']),
            'slug' => Str::slug($title),
            'is_published' => true,
            'user_id' => 1,
            'category_id' => $this->faker->numberBetween(1, 15),
            'read_time' => $readingTime,
            'change_user_id' => 1,
        ];
    }
}

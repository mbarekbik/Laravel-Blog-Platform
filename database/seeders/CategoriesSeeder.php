<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Technologia', 'backgroundColor' => '#3498db', 'textColor' => '#ffffff'],
            ['name' => 'Podróże', 'backgroundColor' => '#2ecc71', 'textColor' => '#ffffff'],
            ['name' => 'Kulinaria', 'backgroundColor' => '#e74c3c', 'textColor' => '#ffffff'],
            ['name' => 'Moda', 'backgroundColor' => '#9b59b6', 'textColor' => '#ffffff'],
            ['name' => 'Zdrowie i Fitness', 'backgroundColor' => '#27ae60', 'textColor' => '#ffffff'],
            ['name' => 'Nauka', 'backgroundColor' => '#3498db', 'textColor' => '#ffffff'],
            ['name' => 'Rozrywka', 'backgroundColor' => '#e67e22', 'textColor' => '#ffffff'],
            ['name' => 'Styl życia', 'backgroundColor' => '#f39c12', 'textColor' => '#ffffff'],
            ['name' => 'Biznes i Finanse', 'backgroundColor' => '#34495e', 'textColor' => '#ffffff'],
            ['name' => 'Edukacja', 'backgroundColor' => '#16a085', 'textColor' => '#ffffff'],
            ['name' => 'Sport', 'backgroundColor' => '#e74c3c', 'textColor' => '#ffffff'],
            ['name' => 'Muzyka', 'backgroundColor' => '#2980b9', 'textColor' => '#ffffff'],
            ['name' => 'Sztuka i Design', 'backgroundColor' => '#8e44ad', 'textColor' => '#ffffff'],
            ['name' => 'DIY', 'backgroundColor' => '#d35400', 'textColor' => '#ffffff'],
            ['name' => 'Gry', 'backgroundColor' => '#c0392b', 'textColor' => '#ffffff'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'backgroundColor' => $category['backgroundColor'],
                'textColor' => $category['textColor'],
            ]);
        }
    }
}

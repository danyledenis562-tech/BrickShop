<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'LEGO City', 'slug' => 'lego-city', 'description' => 'City life adventures', 'sort_order' => 1],
            ['name' => 'LEGO Star Wars', 'slug' => 'lego-star-wars', 'description' => 'Galaxy missions', 'sort_order' => 2],
            ['name' => 'LEGO Technic', 'slug' => 'lego-technic', 'description' => 'Engineering builds', 'sort_order' => 3],
            ['name' => 'LEGO Friends', 'slug' => 'lego-friends', 'description' => 'Heartlake stories', 'sort_order' => 4],
            ['name' => 'LEGO Creator', 'slug' => 'lego-creator', 'description' => 'Creative 3-in-1 builds', 'sort_order' => 5],
            ['name' => 'LEGO Ninjago', 'slug' => 'lego-ninjago', 'description' => 'Ninja action sets', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}

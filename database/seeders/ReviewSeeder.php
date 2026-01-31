<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $comments = [
            'Дуже якісний набір, збирали всією сімʼєю — супер враження.',
            'Деталі ідеально підходять одна до одної, дитина в захваті.',
            'Класний дизайн, багато цікавих елементів для гри.',
            'Збірка не складна, але дуже захоплива.',
            'Все прийшло швидко, набір оригінальний.',
            'Міцна конструкція, виглядає круто на полиці.',
            'Чудовий подарунок, рекомендую.',
            'Дуже сподобалась тематика та персонажі.',
            'Багато дрібних деталей, які додають реалістичності.',
            'Збірка зайняла вечір, результатом дуже задоволені.',
        ];

        foreach (Product::all() as $product) {
            foreach (range(1, 3) as $i) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $users->random()->id,
                    'rating' => rand(4, 5),
                    'comment' => $comments[array_rand($comments)],
                    'approved' => true,
                    'created_at' => now()->subDays(rand(1, 25)),
                    'updated_at' => now(),
                ]);
            }

            Review::create([
                'product_id' => $product->id,
                'user_id' => $users->random()->id,
                'rating' => rand(4, 5),
                'comment' => 'Набір сподобався, чекаю на модерацію відгуку.',
                'approved' => false,
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now(),
            ]);
        }
    }
}

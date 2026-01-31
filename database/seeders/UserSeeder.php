<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@brickshop.test'],
            [
                'name' => 'Brick Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'user@brickshop.test'],
            [
                'name' => 'Brick User',
                'password' => Hash::make('User123!'),
                'role' => 'user',
            ]
        );

        $names = [
            'Олександр Коваленко',
            'Марія Петренко',
            'Ірина Мельник',
            'Дмитро Шевченко',
            'Анна Бондар',
            'Артем Ткаченко',
            'Юлія Савченко',
            'Вікторія Литвин',
            'Максим Дорошенко',
            'Наталія Романюк',
            'Сергій Поліщук',
            'Катерина Черненко',
        ];

        foreach ($names as $index => $name) {
            User::query()->updateOrCreate(
                ['email' => 'user'.($index + 1).'@brickshop.test'],
                [
                    'name' => $name,
                    'password' => Hash::make('User123!'),
                    'role' => 'user',
                ]
            );
        }
    }
}

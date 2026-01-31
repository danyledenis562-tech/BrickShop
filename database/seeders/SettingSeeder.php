<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->updateOrCreate(
            ['id' => 1],
            [
                'phone_support' => '+380800000000',
                'telegram_support_url' => 'https://t.me/brickshop_support',
                'show_support_widget' => true,
            ]
        );
    }
}

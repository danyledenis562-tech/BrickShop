<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            $table->string('locale', 5)->nullable()->after('position');
            $table->timestamp('starts_at')->nullable()->after('sort_order');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'locale', 'starts_at', 'ends_at']);
        });
    }
};

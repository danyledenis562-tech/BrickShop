<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL');
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        } elseif ($driver === 'pgsql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL');
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_email')->nullable()->after('user_id');
            $table->string('tracking_number', 120)->nullable()->after('note');
            $table->string('tracking_url', 500)->nullable()->after('tracking_number');
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('cart_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('cart_json');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_reminders');
        Schema::dropIfExists('newsletter_subscribers');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_email', 'tracking_number', 'tracking_url']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NOT NULL');
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        } elseif ($driver === 'pgsql') {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id SET NOT NULL');
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};

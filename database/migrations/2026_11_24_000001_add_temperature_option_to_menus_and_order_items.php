<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('has_temperature_option')->default(false)->after('has_spicy_level');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('temperature', 10)->nullable()->after('spicy_level');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('has_temperature_option');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('temperature');
        });
    }
};

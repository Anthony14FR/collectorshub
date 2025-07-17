<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('infernal_tower_current_level')->default(1)->after('level');
            $table->integer('infernal_tower_daily_defeats')->default(10)->after('infernal_tower_current_level');
            $table->timestamp('infernal_tower_last_reset')->nullable()->after('infernal_tower_daily_defeats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'infernal_tower_current_level',
                'infernal_tower_daily_defeats',
                'infernal_tower_last_reset'
            ]);
        });
    }
};

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
        Schema::table('pokedex', function (Blueprint $table) {
            $table->integer('team_position')->nullable()->after('is_in_team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokedex', function (Blueprint $table) {
            $table->dropColumn('team_position');
        });
    }
};

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
        Schema::create('infernal_tower_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level')->unique();
            $table->json('team');
            $table->integer('team_cp');
            $table->json('rewards');
            $table->string('trainer_avatar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infernal_tower_levels');
    }
};

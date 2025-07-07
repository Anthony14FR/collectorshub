<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stat_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokedex_id')->constrained()->onDelete('cascade');
            $table->integer('hp_boost')->default(0);
            $table->integer('attack_boost')->default(0);
            $table->integer('defense_boost')->default(0);
            $table->integer('speed_boost')->default(0);
            $table->integer('special_attack_boost')->default(0);
            $table->integer('special_defense_boost')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stat_boosts');
    }
}; 
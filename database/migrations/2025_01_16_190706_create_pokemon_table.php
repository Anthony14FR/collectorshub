<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pokemon', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('pokedex_id');
            $table->string('name', 50);
            $table->json('types');
            $table->json('resistances');
            $table->integer('evolution_id')->nullable();
            $table->integer('pre_evolution_id')->nullable();
            $table->string('description', 250);
            $table->integer('height');
            $table->integer('weight');
            $table->string('rarity', 20);
            $table->boolean('is_shiny')->default(false);
            $table->integer('hp');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('speed');
            $table->integer('special_attack');
            $table->integer('special_defense');
            $table->integer('generation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};

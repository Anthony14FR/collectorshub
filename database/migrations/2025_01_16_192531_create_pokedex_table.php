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
        Schema::create('pokedex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('pokemon_id')->constrained('pokemon');
            $table->integer('pokedex_id');
            $table->string('name', 50);
            $table->string('nickname', 50)->nullable();
            $table->json('types');
            $table->json('resistances');
            $table->integer('evolution_id')->nullable();
            $table->integer('pre_evolution_id')->nullable();
            $table->string('description', 250);
            $table->integer('height');
            $table->integer('weight');
            $table->string('rarity', 20);
            $table->integer('level')->default(1);
            $table->integer('star')->default(0);
            $table->boolean('is_shiny')->default(false);
            $table->integer('hp');
            $table->integer('hp_left')->nullable();
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('speed');
            $table->integer('special_attack');
            $table->integer('special_defense');
            $table->integer('generation')->nullable();
            $table->boolean('is_in_team')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('obtained_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokedex');
    }
};

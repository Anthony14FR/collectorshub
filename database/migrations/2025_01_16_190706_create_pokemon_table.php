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
            $table->id();
            $table->integer('pokedexId');
            $table->string('name');
            $table->json('types');
            $table->json('resistances');
            $table->integer('evolutionId')->nullable();
            $table->integer('preEvolutionId')->nullable();
            $table->string('description');
            $table->integer('height');
            $table->integer('weight');
            $table->string('rarity');
            $table->boolean('isShiny');
            $table->integer('HP');
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('speed');
            $table->integer('special_attack');
            $table->integer('special_defense');
            $table->integer('generation');
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

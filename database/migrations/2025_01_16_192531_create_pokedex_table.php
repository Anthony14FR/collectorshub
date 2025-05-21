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
        Schema::create('pokedexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('pokemon_id')->constrained('pokemon');
            $table->integer('pokedexId');
            $table->string('name', 50);
            $table->string('nickname', 50)->nullable();
            $table->json('types');
            $table->json('resistances');
            $table->integer('evolutionId')->nullable();
            $table->integer('preEvolutionId')->nullable();
            $table->string('description', 250);
            $table->integer('height');
            $table->integer('weight');
            $table->string('rarity', 20);
            $table->integer('level')->default(1);
            $table->integer('star')->default(0);
            $table->boolean('isShiny')->default(false);
            $table->integer('HP');
            $table->integer('hpLeft')->nullable();
            $table->integer('attack');
            $table->integer('defense');
            $table->integer('speed');
            $table->integer('special_attack');
            $table->integer('special_defense');
            $table->integer('generation')->nullable();
            $table->boolean('isInTeam')->default(false);
            $table->boolean('isFavorite')->default(false);
            $table->timestamp('obtainedAt')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokedexes');
    }
};

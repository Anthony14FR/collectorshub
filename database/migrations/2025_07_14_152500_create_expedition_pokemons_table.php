<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('expedition_pokemons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expedition_id')->constrained()->onDelete('cascade');
            $table->foreignId('pokedex_id')->constrained('pokedex')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ends_at');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedition_pokemons');
    }
};

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
            $table->string('nickname', 50)->nullable();
            $table->integer('level')->default(1);
            $table->integer('star')->default(0);
            $table->integer('hp_left')->nullable();
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

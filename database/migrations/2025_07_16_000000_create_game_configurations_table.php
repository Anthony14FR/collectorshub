<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('game_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->index();
            $table->string('key', 100)->index();
            $table->json('value');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['category', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_configurations');
    }
};

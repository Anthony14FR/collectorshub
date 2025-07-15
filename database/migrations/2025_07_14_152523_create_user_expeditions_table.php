<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('user_expeditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expedition_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['available', 'in_progress', 'completed'])->default('available');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'expedition_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_expeditions');
    }
};

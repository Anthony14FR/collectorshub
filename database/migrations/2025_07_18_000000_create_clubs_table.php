<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('icon');
            $table->unsignedBigInteger('leader_id');
            $table->integer('total_cp')->default(0);
            $table->timestamps();

            $table->foreign('leader_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};

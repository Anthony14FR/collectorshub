<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketplace', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pokemon_id')->constrained('pokedex')->onDelete('cascade');
            $table->integer('price');
            $table->enum('status', ['active', 'sold', 'cancelled'])->default('active');
            $table->timestamp('sold_at')->nullable();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketplace');
    }
}; 
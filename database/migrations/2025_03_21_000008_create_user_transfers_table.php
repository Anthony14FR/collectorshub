<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('user_friend_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount')->default(100);
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_friend_gifts');
    }
};

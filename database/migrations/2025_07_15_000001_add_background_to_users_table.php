<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('background')->default('/images/section-me-background.jpg')->after('unlocked_avatars');
            $table->json('unlocked_backgrounds')->default('[]')->after('background');
        });

        DB::table('users')->update([
            'background' => '/images/section-me-background.jpg',
            'unlocked_backgrounds' => json_encode(['/images/section-me-background.jpg'])
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('background');
            $table->dropColumn('unlocked_backgrounds');
        });
    }
};

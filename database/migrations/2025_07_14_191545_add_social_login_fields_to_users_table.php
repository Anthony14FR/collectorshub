<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth fields
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('google_avatar')->nullable()->after('avatar');

            // Discord OAuth fields
            $table->string('discord_id')->nullable()->unique()->after('google_avatar');
            $table->string('discord_username')->nullable()->after('discord_id');
            $table->string('discord_avatar')->nullable()->after('discord_username');

            $table->string('provider')->nullable()->after('discord_avatar');
            $table->timestamp('provider_verified_at')->nullable()->after('provider');

            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_avatar',
                'discord_id',
                'discord_username',
                'discord_avatar',
                'provider',
                'provider_verified_at'
            ]);

            // Restore password as required
            $table->string('password')->nullable(false)->change();
        });
    }
};

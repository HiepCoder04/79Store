<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_ban')) {
                $table->boolean('is_ban')->default(0)->after('email');
            }

            if (!Schema::hasColumn('users', 'ban_reason')) {
                $table->string('ban_reason')->nullable()->after('is_ban');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_ban');
        });
    }
};
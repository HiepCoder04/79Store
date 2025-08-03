<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pots', function (Blueprint $table) {
            $table->integer('quantity')->default(0); // Thêm cột số lượng
        });
    }

    public function down(): void {
        Schema::table('pots', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};

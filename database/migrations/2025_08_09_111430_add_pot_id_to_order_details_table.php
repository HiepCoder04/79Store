<?php

// database/migrations/2025_08_09_000002_add_pot_id_to_order_details_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'pot_id')) {
                $table->unsignedBigInteger('pot_id')->nullable()->after('product_variant_id');
                $table->foreign('pot_id')->references('id')->on('pots')->nullOnDelete();
            }
            // giữ nguyên các cột snapshot/đơn giá đã có
        });
    }
    public function down(): void {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'pot_id')) {
                $table->dropForeign(['pot_id']);
                $table->dropColumn('pot_id');
            }
        });
    }
};

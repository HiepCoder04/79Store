<?php

// database/migrations/2025_08_09_000001_add_delivered_at_to_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->dateTime('delivered_at')->nullable()->after('status');
            }
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivered_at')) {
                $table->dropColumn('delivered_at');
            }
        });
    }
};

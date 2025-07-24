<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'quantity']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0.00);
            $table->integer('quantity')->default(0);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('quantity')->default(0);
        });
    }
};

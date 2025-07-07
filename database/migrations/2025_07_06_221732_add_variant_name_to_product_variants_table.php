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
    Schema::table('product_variants', function (Blueprint $table) {
        $table->string('variant_name')->after('product_id');
    });
}

public function down(): void
{
    Schema::table('product_variants', function (Blueprint $table) {
        $table->dropColumn('variant_name');
    });
}

};

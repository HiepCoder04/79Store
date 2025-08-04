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
        Schema::create('pot_product_variant', function (Blueprint $table) {
            $table->id();
    $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
    $table->foreignId('pot_id')->constrained()->onDelete('cascade');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pot_product_variant');
    }
};

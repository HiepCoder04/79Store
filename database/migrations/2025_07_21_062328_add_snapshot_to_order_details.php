<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('order_details', function (Blueprint $table) {
        
        $table->decimal('product_price', 10, 2);
        $table->string('product_height')->nullable();
        $table->string('product_pot')->nullable();
        $table->decimal('pot_price', 10, 2)->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            //
        });
    }
};

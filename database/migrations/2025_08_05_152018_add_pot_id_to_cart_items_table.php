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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('pot_id')->nullable()->after('product_variant_id');

            $table->foreign('pot_id', 'cart_items_pot_id_foreign')
                ->references('id')
                ->on('pots')
                ->onDelete('set null'); // Khi chậu bị xoá, cart sẽ giữ nguyên nhưng pot_id = null
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign('cart_items_pot_id_foreign');
            $table->dropColumn('pot_id');
        });
    }
};

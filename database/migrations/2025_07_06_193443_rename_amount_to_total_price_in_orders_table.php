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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->nullable()->after('user_id');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('shipping_method')->nullable()->after('payment_method');
            $table->decimal('shipping_fee', 12, 2)->default(0)->after('shipping_method');
            $table->decimal('discount', 12, 2)->default(0)->after('shipping_fee');
            $table->text('note')->nullable()->after('discount');

            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn([
                'address_id',
                'payment_method',
                'shipping_method',
                'shipping_fee',
                'discount',
                'note'
            ]);
        });
    }
};

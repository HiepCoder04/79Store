<?php

// database/migrations/2025_08_09_000003_create_return_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_detail_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedBigInteger('pot_id')->nullable();

            $table->unsignedInteger('quantity');
            $table->string('reason', 500)->nullable();
            $table->json('images')->nullable();

            $table->enum('status', ['pending','approved','rejected','refunded','exchanged'])->default('pending');

            // thông tin ngân hàng khách
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->string('bank_account_number', 50)->nullable();

            // vận chuyển hàng trả (nếu có)
            $table->string('tracking_code', 120)->nullable();

            // admin
            $table->text('admin_note')->nullable();
            $table->dateTime('resolved_at')->nullable();

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('order_detail_id')->references('id')->on('order_details')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->foreign('pot_id')->references('id')->on('pots')->nullOnDelete();

            $table->index(['status', 'order_id', 'user_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('return_requests');
    }
};

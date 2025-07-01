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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2)->default(0);

            $table->string('order_status')->default('pending'); // Trạng thái đơn: pending, processed_and_ready_to_ship, shipped, delivered, return, canceled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded

            $table->json('point_method')->nullable(); // dùng để lưu điểm thưởng đã sử dụng nếu có

            $table->softDeletes(); // để xóa mềm
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->decimal('amount', 12, 2)->default(0); // tổng tiền đơn hàng
            $table->string('order_status')->default('pending'); // trạng thái đơn hàng
            $table->string('payment_status')->default('unpaid'); // trạng thái thanh toán

            $table->json('point_method')->nullable(); // lưu thông tin điểm thưởng
            $table->softDeletes(); // thêm cột deleted_at (xóa mềm)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bước 1: Xóa foreign key nếu tồn tại
        try {
            DB::statement('ALTER TABLE order_details DROP FOREIGN KEY order_details_user_id_foreign');
        } catch (\Exception $e) {
            // Bỏ qua nếu không tồn tại
        }

        // Bước 2: Xoá các cột thông thường nếu tồn tại
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('order_details', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('order_details', 'order_status')) {
                $table->dropColumn('order_status');
            }
            if (Schema::hasColumn('order_details', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('order_details', 'point_method')) {
                $table->dropColumn('point_method');
            }
        });

        // Bước 3: Xoá cột deleted_at nếu tồn tại
        if (Schema::hasColumn('order_details', 'deleted_at')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

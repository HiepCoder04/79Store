<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột deleted_at vào bảng order_details để hỗ trợ soft deletes.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Xóa cột deleted_at nếu rollback migration.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

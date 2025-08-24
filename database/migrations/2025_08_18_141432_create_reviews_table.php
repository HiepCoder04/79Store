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
         Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 sao
            $table->text('comment')->nullable();
            $table->string('image_path')->nullable(); // 1 ảnh minh họa
            $table->text('admin_reply')->nullable();  // phản hồi từ admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

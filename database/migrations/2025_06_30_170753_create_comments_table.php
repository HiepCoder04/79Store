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
        Schema::create('comments', function (Blueprint $table) {
          $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // liên kết user
            $table->foreignId('product_id')->constrained()->onDelete('cascade');          // liên kết sản phẩm

            $table->string('name')->nullable();     // tên người bình luận (guest)
            $table->string('email')->nullable();    // email người bình luận (guest)
            $table->unsignedTinyInteger('rating')->nullable(); // điểm đánh giá (1–5)
            $table->text('content');                // nội dung bình luận
            $table->unsignedBigInteger('parent_id')->nullable(); // để trả lời bình luận cha
            $table->boolean('is_admin')->default(false);  // có phải admin không
            $table->boolean('is_hidden')->default(false); // có bị ẩn không (ban tạm thời)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

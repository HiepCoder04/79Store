<?php

// database/migrations/2025_08_09_000004_create_return_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('return_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_request_id');

            $table->enum('type', ['refund','exchange']);
            $table->unsignedBigInteger('amount')->default(0); // VND

            $table->string('note', 500)->nullable();

            // bank log khi hoàn tiền thủ công
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->json('proof_images')->nullable(); // Thay bank_transfer_ref thành proof_images

            $table->dateTime('processed_at')->nullable();

            $table->timestamps();

            $table->foreign('return_request_id')->references('id')->on('return_requests')->cascadeOnDelete();
            $table->index(['type', 'processed_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('return_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Reviewer — nullable so reviews survive account deletion
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // Frozen reviewer info (in case account is deleted)
            $table->string('author_name');
            $table->string('author_email');

            // Review content
            $table->unsignedTinyInteger('rating');       // 1–5
            $table->string('title')->nullable();
            $table->text('body')->nullable();

            // Moderation: pending | approved | rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('customer_id');

            // One review per customer per product
            $table->unique(['product_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

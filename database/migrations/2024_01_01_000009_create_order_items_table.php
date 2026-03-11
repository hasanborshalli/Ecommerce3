<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Frozen snapshot — immune to future product edits
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('product_price', 10, 2);    // selling price at time of order
            $table->decimal('product_cost', 10, 2)->default(0); // cost_price at time of order

            $table->integer('quantity');
            $table->json('variant')->nullable();          // {"Size":"M","Color":"Black"}

            $table->decimal('line_total', 10, 2);         // product_price × quantity
            $table->decimal('line_cost', 10, 2)->default(0);
            $table->decimal('line_profit', 10, 2)->default(0);

            $table->timestampsTz();

            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
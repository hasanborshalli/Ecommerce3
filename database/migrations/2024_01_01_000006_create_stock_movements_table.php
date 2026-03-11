<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Type: order_placed, order_cancelled, purchase_received,
            //       manual_adjustment, manual_addition, initial_stock
            $table->string('type');

            $table->integer('quantity_before');
            $table->integer('quantity_change');  // positive = stock added, negative = deducted
            $table->integer('quantity_after');

            // Optional reference to the source record
            $table->string('reference_type')->nullable();  // 'order' | 'purchase_order' | 'manual'
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->string('notes')->nullable();
            $table->timestampsTz();

            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
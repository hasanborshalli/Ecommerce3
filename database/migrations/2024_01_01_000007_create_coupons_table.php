<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Coupons ───────────────────────────────────────────────
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // e.g. SUMMER20
            $table->string('description')->nullable();  // admin-facing note

            // Type: percentage | fixed | free_shipping
            $table->enum('type', ['percentage', 'fixed', 'free_shipping'])->default('percentage');
            $table->decimal('value', 10, 2)->default(0); // 20 = 20% off or $20 off

            // Constraints
            $table->decimal('min_order_amount', 10, 2)->default(0); // 0 = no minimum
            $table->unsignedInteger('max_uses')->nullable();         // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('max_uses_per_customer')->default(1);

            $table->boolean('is_active')->default(true);
            $table->timestampTz('expires_at')->nullable();
            $table->timestampsTz();

            $table->index('code');
            $table->index('is_active');
        });

        // coupon_uses is created in migration 000012 (after orders table exists)
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
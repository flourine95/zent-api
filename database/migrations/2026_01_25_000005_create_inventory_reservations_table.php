<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inventory_id');
            $table->foreign('inventory_id')->references('id')->on('inventories')->cascadeOnDelete();
            $table->uuid('order_id')->nullable();
            $table->uuid('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();

            $table->integer('quantity');
            $table->string('status')->default('pending'); // pending, confirmed, released, expired
            $table->text('notes')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};

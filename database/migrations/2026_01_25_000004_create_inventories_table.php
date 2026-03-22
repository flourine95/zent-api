<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->uuid('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();

            $table->integer('quantity')->default(0);
            $table->string('shelf_location')->nullable();

            $table->timestamps();
            $table->unique(['warehouse_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

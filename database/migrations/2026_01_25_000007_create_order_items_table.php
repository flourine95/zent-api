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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants');
            $table->foreignId('warehouse_id')->nullable()->constrained();

            $table->integer('quantity');
            $table->decimal('price', 15, 2);

            // Snapshot lưu lại tên/options lúc mua phòng khi sản phẩm bị xóa/sửa
            $table->jsonb('product_snapshot')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

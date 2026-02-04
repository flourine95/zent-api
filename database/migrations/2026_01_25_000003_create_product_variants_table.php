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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->string('sku')->unique();
            $table->decimal('price', 15);
            $table->decimal('original_price', 15)->nullable();
            $table->string('image')->nullable();

            // JSONB: Lưu Size/Color (Vd: {"size": "L", "color": "Red"})
            $table->jsonb('options');

            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('CREATE INDEX product_variants_options_gin_index ON product_variants USING gin (options)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            $table->json('name'); // Translatable field
            $table->string('slug')->unique();
            $table->json('description')->nullable(); // Translatable field
            $table->string('thumbnail')->nullable();

            // JSONB: Lưu thông số (Brand, Material...)
            $table->jsonb('specs')->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('CREATE INDEX products_specs_gin_index ON products USING gin (specs)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

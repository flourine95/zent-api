<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipping_providers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ghtk, ghn, viettel, jnt
            $table->string('name');
            $table->boolean('is_active')->default(false); // Disabled by default until admin configures
            $table->json('config')->nullable(); // Non-sensitive config (endpoints, default_pickup)
            $table->integer('priority')->default(0); // For auto-selection
            $table->timestamps();
        });

        // Note: Run ShippingProviderSeeder to populate providers
        // php artisan db:seed --class=ShippingProviderSeeder
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_providers');
    }
};

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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->constrained('shipping_providers');

            // Provider's order ID (partner_id for GHTK)
            $table->string('provider_order_id')->index();
            $table->unique(['provider_id', 'provider_order_id']);

            // Tracking info
            $table->string('tracking_number')->nullable()->index();
            $table->string('label_id')->nullable();

            // Standardized status (normalized across providers)
            $table->string('status')->default('pending'); // pending, picked, in_transit, delivered, returned, cancelled
            $table->string('provider_status')->nullable(); // Original status from provider
            $table->text('status_note')->nullable();

            // Pricing
            $table->integer('fee')->nullable();
            $table->integer('insurance_fee')->nullable();
            $table->integer('cod_amount')->default(0);
            $table->integer('declared_value')->default(0);
            $table->integer('weight')->default(0); // grams

            // Customer info (JSON for flexibility)
            $table->json('customer_info'); // name, tel, address, province, district, ward

            // Pickup info (JSON)
            $table->json('pickup_info')->nullable();

            // Estimated times
            $table->timestamp('estimated_pickup_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('actual_delivery_at')->nullable();

            // Provider-specific data
            $table->json('provider_metadata')->nullable(); // Store provider-specific fields
            $table->json('products')->nullable();

            // Options
            $table->boolean('is_freeship')->default(false);
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->uuid('provider_id');
            $table->foreign('provider_id')->references('id')->on('shipping_providers');

            $table->string('provider_order_id')->index();
            $table->unique(['provider_id', 'provider_order_id']);

            $table->string('tracking_number')->nullable()->index();
            $table->string('label_id')->nullable();

            $table->string('status')->default('pending');
            $table->string('provider_status')->nullable();
            $table->text('status_note')->nullable();

            $table->integer('fee')->nullable();
            $table->integer('insurance_fee')->nullable();
            $table->integer('cod_amount')->default(0);
            $table->integer('declared_value')->default(0);
            $table->integer('weight')->default(0);

            $table->json('customer_info');
            $table->json('pickup_info')->nullable();

            $table->timestamp('estimated_pickup_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('actual_delivery_at')->nullable();

            $table->json('provider_metadata')->nullable();
            $table->json('products')->nullable();

            $table->boolean('is_freeship')->default(false);
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

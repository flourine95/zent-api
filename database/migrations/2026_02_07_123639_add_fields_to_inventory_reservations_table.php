<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_reservations', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->after('inventory_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->after('quantity'); // pending, confirmed, released, expired
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_reservations', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropForeign(['order_id']);
            $table->dropColumn(['inventory_id', 'order_id', 'status', 'notes']);
        });
    }
};

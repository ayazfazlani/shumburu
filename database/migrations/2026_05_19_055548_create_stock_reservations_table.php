<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('stock_reservations')) {
            Schema::create('stock_reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
                $table->foreignId('fg_stock_id')->constrained('fg_stock')->onDelete('cascade');
                $table->decimal('quantity', 15, 2);
                $table->string('status')->default('active'); // active, consumed, cancelled
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};

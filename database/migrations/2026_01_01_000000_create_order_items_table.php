<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->notNull();
            $table->string('unit', 20)->default('meter');
            $table->decimal('unit_price', 10, 2)->notNull();
            $table->decimal('total_price', 12, 2)->notNull();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('production_order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}; 
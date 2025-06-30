<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('deliveries', function (Blueprint $table) {
      $table->id();
      $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
      $table->foreignId('customer_id')->constrained()->onDelete('cascade');
      $table->foreignId('product_id')->constrained()->onDelete('cascade');
      $table->decimal('quantity', 10, 2);
      $table->string('batch_number');
      $table->decimal('unit_price', 10, 2);
      $table->decimal('total_amount', 12, 2);
      $table->date('delivery_date');
      $table->foreignId('delivered_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('deliveries');
  }
};

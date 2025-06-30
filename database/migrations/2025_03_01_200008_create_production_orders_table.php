<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('production_orders', function (Blueprint $table) {
      $table->id();
      $table->string('order_number')->unique();
      $table->foreignId('customer_id')->constrained()->onDelete('cascade');
      $table->foreignId('product_id')->constrained()->onDelete('cascade');
      $table->decimal('quantity', 10, 2);
      $table->enum('status', ['pending', 'approved', 'in_production', 'completed', 'delivered'])->default('pending');
      $table->date('requested_date');
      $table->date('production_start_date')->nullable();
      $table->date('production_end_date')->nullable();
      $table->date('delivery_date')->nullable();
      $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
      $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('cascade');
      $table->foreignId('plant_manager_id')->nullable()->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('production_orders');
  }
};

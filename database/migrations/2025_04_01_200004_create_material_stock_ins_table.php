<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('material_stock_ins', function (Blueprint $table) {
      $table->id();
      $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
      $table->decimal('quantity', 10, 3); // Weight in kg
      $table->string('batch_number');
      $table->date('received_date');
      $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('material_stock_ins');
  }
};

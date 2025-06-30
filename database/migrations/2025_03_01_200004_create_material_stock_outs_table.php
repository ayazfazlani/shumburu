<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('material_stock_outs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
      $table->decimal('quantity', 10, 3); // Weight in kg
      $table->string('batch_number');
      $table->date('issued_date');
      $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
      $table->enum('status', ['material_on_process', 'completed', 'scrapped'])->default('material_on_process');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('material_stock_outs');
  }
};

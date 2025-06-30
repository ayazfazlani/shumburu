<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('scrap_waste', function (Blueprint $table) {
      $table->id();
      // $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
      // $table->foreignId('production_line_id')->constrained('production_lines');
      $table->foreignId('material_stock_out_line_id')->constrained('material_stock_out_lines');
      $table->decimal('quantity', 10, 3); // Weight in kg
      $table->string('reason');
      $table->date('waste_date');
      $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('scrap_waste');
  }
};
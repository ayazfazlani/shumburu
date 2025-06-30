<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('finished_goods', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')->constrained()->onDelete('cascade');
      // $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
      // $table->foreignId('production_line_id')->constrained('production_lines');
      $table->foreignId('material_stock_out_line_id')->constrained('material_stock_out_lines');
      $table->enum('type', ['roll', 'cut']);
      $table->decimal('length_m', 6, 2);
      $table->decimal('outer_diameter', 8, 3)->nullable();
      $table->integer('quantity');
      $table->decimal('total_weight', 10, 3)->nullable(); // can be calculated
      $table->string('size')->nullable();
      $table->string('surface')->nullable();
      $table->decimal('thickness', 6, 3)->nullable();
      $table->decimal('ovality', 5, 3)->nullable();
      $table->string('batch_number');
      $table->date('production_date');
      $table->enum('purpose', ['for_stock', 'for_customer_order'])->default('for_stock');
      $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
      $table->foreignId('produced_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('finished_goods');
  }
};

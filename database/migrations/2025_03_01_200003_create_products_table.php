<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('code')->unique(); // OS1, OS2, OS3, etc.
      $table->string('size'); // 20mm, 32mm, 110mm, etc.
      $table->string('pn'); // PN6, PN10, etc.
      $table->decimal('weight_per_meter', 8, 3)->nullable();
      $table->decimal('meter_length', 8, 2);
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
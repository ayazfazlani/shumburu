<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('downtime_records', function (Blueprint $table) {
      $table->id();
      $table->date('downtime_date');
      $table->time('start_time');
      $table->time('end_time');
      $table->integer('duration_minutes');
      $table->string('reason');
      $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('downtime_records');
  }
};

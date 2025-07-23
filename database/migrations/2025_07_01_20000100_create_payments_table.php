<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
      $table->foreignId('customer_id')->constrained()->onDelete('cascade');
      $table->decimal('amount', 12, 2);
      $table->string('payment_method')->nullable();
      $table->string('bank_slip_reference')->nullable();
      $table->string('proforma_invoice_number')->nullable();
      $table->date('payment_date');
      $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('payments');
  }
};

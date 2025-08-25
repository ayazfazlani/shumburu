<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fya_warehouses', function (Blueprint $table) {
            $table->id();
            // Link to finished good record for traceability
            $table->foreignId('finished_good_id')->constrained()->onDelete('cascade');
            // Movement direction: in or out of FYA
            $table->enum('movement_type', ['in', 'out'])->default('in');
            // Core fields
            $table->decimal('quantity', 12, 3); // pieces or meters depending on product type
            $table->string('batch_number');
            $table->enum('purpose', ['for_stock', 'for_customer_order'])->default('for_stock');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->date('movement_date');
            // Optional reference to external entities (delivery, transfer, etc.)
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
            // Useful indexes for reporting
            $table->index(['batch_number']);
            $table->index(['movement_date']);
            $table->index(['purpose']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fya_warehouses');
    }
};

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
        Schema::create('material_stock_out_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_stock_out_id')->constrained('material_stock_outs')->onDelete('cascade');
            $table->foreignId('production_line_id')->constrained('production_lines')->onDelete('cascade');
            $table->decimal('quantity_consumed', 10, 2);
            $table->timestamps();
            $table->string('shift')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_stock_out_lines');
    }
};

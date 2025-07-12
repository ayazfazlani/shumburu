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
        Schema::create('finished_good_stock_out_line', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finished_good_id')->constrained('finished_goods')->onDelete('cascade');
            $table->foreignId('material_stock_out_line_id')->constrained('material_stock_out_lines')->onDelete('cascade');
            $table->decimal('quantity_used', 10, 2)->nullable(); // Optional: how much of this line was used for this finished good
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finished_good_material_stock_out_line');
    }
};

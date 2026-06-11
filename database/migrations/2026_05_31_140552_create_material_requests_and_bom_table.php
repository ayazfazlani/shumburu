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
        Schema::create('product_raw_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->decimal('ratio', 10, 4)->default(1.0)->comment('Percentage or kg per unit');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_request_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('raw_material_id')->constrained();
            $table->decimal('quantity', 15, 2);
            $table->string('status')->default('pending'); // pending, approved, issued, purchase_raised
            $table->foreignId('requested_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
        Schema::dropIfExists('product_raw_material');
    }
};

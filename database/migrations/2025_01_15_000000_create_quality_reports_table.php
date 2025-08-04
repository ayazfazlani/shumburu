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
        Schema::create('quality_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', ['daily', 'weekly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('quality_comment')->nullable();
            $table->text('problems')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('remarks')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['report_type', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_reports');
    }
}; 
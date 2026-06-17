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
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('production_line_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('draft'); // draft, approved, in_production, completed
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->decimal('planned_quantity', 15, 2);
            $table->timestamps();
        });

        Schema::table('material_requests', function (Blueprint $table) {
            $table->foreignId('production_plan_id')->nullable()->constrained()->onDelete('set null')->after('production_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign(['production_plan_id']);
            $table->dropColumn('production_plan_id');
        });
        Schema::dropIfExists('production_plan_items');
        Schema::dropIfExists('production_plans');
    }
};

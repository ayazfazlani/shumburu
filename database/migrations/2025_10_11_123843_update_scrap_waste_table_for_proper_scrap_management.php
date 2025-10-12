<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scrap_waste', function (Blueprint $table) {
            // Add new columns for proper scrap management
            $table->string('scrap_type')->default('raw_material')->after('material_stock_out_line_id'); // 'raw_material' or 'finished_goods'
            $table->unsignedBigInteger('finished_good_id')->nullable()->after('scrap_type');
            $table->boolean('is_repressible')->default(false)->after('notes'); // Can this scrap be reused?
            $table->string('disposal_method')->default('dispose')->after('is_repressible'); // 'dispose', 'reprocess', 'return_to_supplier'
            $table->decimal('cost', 10, 2)->nullable()->after('disposal_method');
            $table->string('status')->default('pending')->after('cost'); // 'pending', 'approved', 'rejected'
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Add foreign key constraints
            $table->foreign('finished_good_id')->references('id')->on('finished_goods')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index(['scrap_type', 'waste_date']);
            $table->index(['is_repressible', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('scrap_waste', function (Blueprint $table) {
            $table->dropForeign(['finished_good_id']);
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['scrap_type', 'waste_date']);
            $table->dropIndex(['is_repressible', 'status']);
            
            $table->dropColumn([
                'scrap_type',
                'finished_good_id',
                'is_repressible',
                'disposal_method',
                'cost',
                'status',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};
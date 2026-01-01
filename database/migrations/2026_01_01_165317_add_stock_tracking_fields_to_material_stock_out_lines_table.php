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
        Schema::table('material_stock_out_lines', function (Blueprint $table) {
            $table->decimal('quantity_returned', 10, 2)->default(0)->after('quantity_consumed');
            $table->text('return_notes')->nullable()->after('quantity_returned');
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null')->after('return_notes');
            $table->timestamp('returned_at')->nullable()->after('returned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_stock_out_lines', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['quantity_returned', 'return_notes', 'returned_by', 'returned_at']);
        });
    }
};

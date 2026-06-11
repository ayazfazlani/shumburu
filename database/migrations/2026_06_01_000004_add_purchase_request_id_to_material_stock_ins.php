<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_stock_ins', function (Blueprint $table) {
            $table->foreignId('purchase_request_id')->nullable()->constrained('purchase_requests')->onDelete('set null')->after('raw_material_id');
        });
    }

    public function down(): void
    {
        Schema::table('material_stock_ins', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn('purchase_request_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null')->after('requested_by');
            $table->string('po_number')->nullable()->unique()->after('supplier_id');
            $table->decimal('unit_price', 15, 4)->nullable()->after('po_number');
            $table->date('expected_delivery_date')->nullable()->after('unit_price');
            $table->timestamp('po_issued_at')->nullable()->after('expected_delivery_date');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('po_issued_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('delivered_at')->nullable()->after('approved_at');
            $table->timestamp('received_at')->nullable()->after('delivered_at');
            // status progression: pending → approved → po_issued → delivered → received
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['supplier_id', 'po_number', 'unit_price', 'expected_delivery_date', 'po_issued_at', 'approved_by', 'approved_at', 'delivered_at', 'received_at']);
        });
    }
};

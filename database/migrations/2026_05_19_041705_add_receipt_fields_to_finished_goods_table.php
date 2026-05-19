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
        Schema::table('finished_goods', function (Blueprint $table) {
            $table->string('status')->default('pending'); // pending, received
            $table->decimal('received_quantity', 15, 2)->nullable();
            $table->text('receipt_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finished_goods', function (Blueprint $table) {
            $table->dropColumn(['status', 'received_quantity', 'receipt_notes']);
        });
    }
};

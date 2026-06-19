<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'pending_production',
                'approved',
                'in_production',
                'completed',
                'delivered'
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CRITICAL: Clean up or remap rows with 'pending_production' status before changing the ENUM constraints back!
        DB::table('production_orders')
            ->where('status', 'pending_production')
            ->update(['status' => 'pending']);

        Schema::table('production_orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'in_production',
                'completed',
                'delivered'
            ])->default('pending')->change();
        });
    }
};

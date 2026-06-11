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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('od')->nullable()->after('quantity')->comment('Outer Diameter');
            $table->string('pn')->nullable()->after('od')->comment('Pressure Nominal');
            $table->string('sdr')->nullable()->after('pn')->comment('Standard Dimension Ratio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['od', 'pn', 'sdr']);
        });
    }
};

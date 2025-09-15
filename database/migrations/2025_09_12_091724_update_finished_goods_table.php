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
            // Rename existing column
            $table->renameColumn('ovality', 'start_ovality');

            // Add new column
            $table->decimal('end_ovality', 8, 2)->nullable()->after('start_ovality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finished_goods', function (Blueprint $table) {
             // Rollback changes
            $table->renameColumn('start_ovality', 'ovality');
            $table->dropColumn('end_ovality');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('code')->nullable()->change();
            $table->decimal('meter_length', 8, 2)->nullable()->change();
            $table->dropUnique('products_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('code')->nullable(false)->change();
            $table->decimal('meter_length', 8, 2)->nullable(false)->change();
            $table->unique('code', 'products_code_unique');
        });
    }
};

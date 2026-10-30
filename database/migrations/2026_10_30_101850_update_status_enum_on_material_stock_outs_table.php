<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statuses allowed for material_stock_outs.status.
     * Keep this in sync with MaterialStockOut::STATUSES.
     */
    private array $statuses = [
        'material_on_process',
        'completed',
        'scrapped',
        'returned',      // ← new: material returned from process
        'cancelled',     // ← new: movement voided / corrected
    ];

    public function up(): void
    {
        // ENUM change requires raw SQL — Laravel's Schema builder
        // doesn't support adding values to an existing ENUM.
        $values = implode("','", $this->statuses);

        DB::statement("
            ALTER TABLE material_stock_outs
            MODIFY COLUMN status ENUM('{$values}')
            NOT NULL DEFAULT 'material_on_process'
        ");
    }

    public function down(): void
    {
        // Revert to the original three values.
        // First, normalize any rows holding the new values so the
        // ALTER doesn't truncate / error.
        DB::statement("
            UPDATE material_stock_outs
            SET status = 'material_on_process'
            WHERE status IN ('returned', 'cancelled')
        ");

        DB::statement("
            ALTER TABLE material_stock_outs
            MODIFY COLUMN status ENUM(
                'material_on_process',
                'completed',
                'scrapped'
            ) NOT NULL DEFAULT 'material_on_process'
        ");
    }
};

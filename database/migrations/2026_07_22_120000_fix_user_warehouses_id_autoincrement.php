<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Some environments have a `user_warehouses` table whose `id` column lost its
 * AUTO_INCREMENT attribute (typically after a mysqldump/import). That makes every
 * pivot insert fail with "Field 'id' doesn't have a default value" when assigning
 * a warehouse manager. This migration restores AUTO_INCREMENT only when missing,
 * so it is a no-op on databases that are already correct.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_warehouses')) {
            return;
        }

        $database = DB::getDatabaseName();

        $column = DB::selectOne(
            'SELECT COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$database, 'user_warehouses', 'id']
        );

        // Column missing entirely — nothing safe to do here.
        if (!$column) {
            return;
        }

        // Already auto-increment: no change needed (safe on production).
        if (str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
            return;
        }

        // AUTO_INCREMENT requires the column to be a key. Ensure a PRIMARY KEY on id.
        if (strtoupper((string) $column->COLUMN_KEY) !== 'PRI') {
            $hasPrimary = DB::selectOne(
                "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.STATISTICS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = 'PRIMARY'",
                [$database, 'user_warehouses']
            );

            if ((int) $hasPrimary->c === 0) {
                DB::statement('ALTER TABLE `user_warehouses` ADD PRIMARY KEY (`id`)');
            }
        }

        DB::statement('ALTER TABLE `user_warehouses` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        // Intentionally irreversible: reverting would reintroduce the bug.
    }
};

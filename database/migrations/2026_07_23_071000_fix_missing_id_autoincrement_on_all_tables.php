<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * After mysqldump/import, some tables can lose AUTO_INCREMENT on their integer
 * `id` primary key. Inserts then fail with:
 *   SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value
 *
 * This migration restores AUTO_INCREMENT for every integer `id` column that is
 * missing it. String/UUID `id` columns (oauth_*, sessions, system_notifications,
 * etc.) are intentionally skipped.
 *
 * Safe for production: already-correct tables are skipped (no-op).
 */
return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();

        $columns = DB::select(
            'SELECT TABLE_NAME, COLUMN_TYPE, COLUMN_KEY, EXTRA, DATA_TYPE
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ?
               AND COLUMN_NAME = ?
               AND DATA_TYPE IN (?, ?, ?, ?, ?)
             ORDER BY TABLE_NAME',
            [$database, 'id', 'tinyint', 'smallint', 'mediumint', 'int', 'bigint']
        );

        foreach ($columns as $column) {
            $table = (string) $column->TABLE_NAME;

            if (!Schema::hasTable($table)) {
                continue;
            }

            if (str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
                continue;
            }

            // AUTO_INCREMENT requires the column to be indexed/keyed.
            if (strtoupper((string) $column->COLUMN_KEY) !== 'PRI') {
                $hasPrimary = DB::selectOne(
                    "SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = 'PRIMARY'",
                    [$database, $table]
                );

                if ((int) ($hasPrimary->c ?? 0) === 0) {
                    DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
                }
            }

            // Preserve the existing integer type/unsigned/zerofill attributes.
            $columnType = strtoupper((string) $column->COLUMN_TYPE);
            DB::statement("ALTER TABLE `{$table}` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT");
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: reverting would reintroduce insert failures.
    }
};

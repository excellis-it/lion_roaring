<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeModelMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example:
     * php artisan make:model-migration User add_profile_photo
     */
    protected $signature = 'make:model-migration {model} {field}';

    /**
     * The console command description.
     */
    protected $description = 'Create a migration to add a field to the table of a given model.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $modelName = $this->argument('model');
            $fieldName = $this->argument('field');

            $modelClass = "App\\Models\\{$modelName}";

            if (!class_exists($modelClass)) {
                $this->error("❌ Model not found: {$modelClass}");
                return Command::FAILURE;
            }

            $modelInstance = new $modelClass();
            $tableName = $modelInstance->getTable();

            $migrationName = "add_{$fieldName}_to_{$tableName}_table";

            Artisan::call('make:migration', [
                'name' => $migrationName,
                '--table' => $tableName,
            ]);

            $this->info("✅ Migration created: {$migrationName}");
            $this->info("💡 Table detected: {$tableName}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('⚠️ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

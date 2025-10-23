<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CmsContent;

class MigrateCmsTableDataToCmsContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:migrate-data-to-cms-contents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate CMS table data to CMS contents table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // migrate cms table data to cms_contents table
        $models = [
            \App\Models\EcomFooterCms::class,
            // \App\Models\HomeBannerCms::class,
            // add all CMS models here
        ];

        foreach ($models as $modelClass) {
            $all = $modelClass::all();
            foreach ($all as $item) {
                $item->syncCmsContent('US'); // default country
                $this->info("Migrated {$modelClass} ID: {$item->id}");
            }
        }

        $this->info('Migration completed!');
    }
}

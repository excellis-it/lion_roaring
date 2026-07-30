<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\GlobalImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * Chat attachments uploaded before HEIC/HEIF/TIFF normalisation are stored in
 * their source container, so they render as a plain download card instead of an
 * image bubble. This rewrites those rows to a JPEG sibling.
 */
class ConvertLegacyChatMedia extends Command
{
    protected $signature = 'chat:convert-legacy-media {--dry-run : List what would change without writing}';

    protected $description = 'Convert legacy chat/team-chat image attachments (HEIC, HEIF, TIFF) to browser-renderable JPEG';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $disk = Storage::disk('public');
        $converted = 0;
        $skipped = 0;

        foreach (['chats', 'team_chats'] as $table) {
            $rows = DB::table($table)
                ->whereNotNull('attachment')
                ->where('attachment', '!=', '')
                ->select('id', 'attachment')
                ->get();

            foreach ($rows as $row) {
                $ext = Helper::mediaExtension($row->attachment);

                if (! Helper::isChatImageExtension($ext) || Helper::isChatInlineImageExtension($ext)) {
                    continue;
                }

                if (! $disk->exists($row->attachment)) {
                    $this->warn("[$table #{$row->id}] missing on disk: {$row->attachment}");
                    $skipped++;
                    continue;
                }

                $target = preg_replace('/\.[^.\/]+$/', '', $row->attachment).'_converted.jpg';

                if ($dryRun) {
                    $this->line("[$table #{$row->id}] {$row->attachment} -> {$target}");
                    $converted++;
                    continue;
                }

                try {
                    $manager = extension_loaded('imagick') ? ImageManager::imagick() : ImageManager::gd();
                    $image = $manager->read($disk->path($row->attachment));
                    $jpeg = $image->encode(new \Intervention\Image\Encoders\JpegEncoder(85));
                    $disk->put($target, (string) $jpeg);
                } catch (\Throwable $e) {
                    $this->error("[$table #{$row->id}] convert failed: ".$e->getMessage());
                    $skipped++;
                    continue;
                }

                // Keep the source reachable so the download card still works.
                GlobalImage::create([
                    'original_path' => $row->attachment,
                    'compressed_path' => $target,
                ]);

                DB::table($table)->where('id', $row->id)->update(['attachment' => $target]);
                $this->info("[$table #{$row->id}] converted to {$target}");
                $converted++;
            }
        }

        $this->line(($dryRun ? 'Would convert' : 'Converted').": {$converted}, skipped: {$skipped}");

        return self::SUCCESS;
    }
}

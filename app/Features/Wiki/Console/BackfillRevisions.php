<?php

declare(strict_types=1);

namespace App\Features\Wiki\Console;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;
use App\Shared\Services\GitService;
use Illuminate\Console\Command;

/**
 * Creates revision rows for commits made before revision tracking existed.
 */
class BackfillRevisions extends Command
{
    protected $signature = 'wiki:backfill-revisions';

    protected $description = 'Create revision rows from the wiki Git history of every page';

    public function handle(GitService $git): int
    {
        $created = 0;

        foreach (Page::all() as $page) {
            foreach ($git->log($page->getMarkdownFilePath()) as $entry) {
                $revision = Revision::firstOrCreate(
                    ['page_id' => $page->id, 'sha' => $entry['sha']],
                    [
                        'path' => $entry['path'],
                        'author' => $page->author,
                        'summary' => $entry['subject'],
                        'created_at' => $entry['date'],
                    ],
                );
                $created += $revision->wasRecentlyCreated ? 1 : 0;
            }
        }

        $this->info("Created {$created} revision(s).");

        return self::SUCCESS;
    }
}

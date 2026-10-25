<?php

declare(strict_types=1);

namespace App\Features\Wiki\Console;

use App\Features\Wiki\Actions\SyncPageLinks;
use App\Features\Wiki\Models\Page;
use Illuminate\Console\Command;

/**
 * Rebuilds the link table for pages saved before link tracking existed.
 */
class SyncLinks extends Command
{
    protected $signature = 'wiki:sync-links';

    protected $description = 'Rebuild the [[wiki link]] table from the content of every page';

    public function handle(SyncPageLinks $syncLinks): int
    {
        $count = 0;

        foreach (Page::all() as $page) {
            $syncLinks->execute($page);
            $count++;
        }

        $this->info("Synced links for {$count} page(s).");

        return self::SUCCESS;
    }
}

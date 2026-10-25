<?php

declare(strict_types=1);

namespace App\Features\Wiki\Observers;

use App\Features\Wiki\Actions\SyncPageLinks;
use App\Features\Wiki\Models\Page;

/**
 * Every write path (editor, revert, seeder) saves through the model, so links are synced here.
 */
final class PageObserver
{
    public function __construct(private SyncPageLinks $syncLinks) {}

    public function saved(Page $page): void
    {
        if ($page->wasRecentlyCreated || $page->wasChanged('content')) {
            $this->syncLinks->execute($page);
        }
    }
}

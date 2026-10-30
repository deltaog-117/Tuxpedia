<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;

final class RecordRevision
{
    /**
     * Link a page to a commit. Recording the same commit twice returns the existing row.
     */
    public function execute(Page $page, string $sha, ?string $author, ?string $summary, ?int $userId = null): Revision
    {
        return Revision::firstOrCreate(
            ['page_id' => $page->id, 'sha' => $sha],
            [
                'path' => $page->getMarkdownFilePath(),
                'author' => $author,
                'user_id' => $userId,
                'summary' => $summary !== null && trim($summary) !== '' ? trim($summary) : null,
            ],
        );
    }
}

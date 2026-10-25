<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;

final class RevertPage
{
    public function __construct(
        private ReadRevision $read,
        private UpdatePage $update,
    ) {}

    /**
     * Save an old revision's content as a new revision; history is never rewritten.
     *
     * @return bool false when the page already has that content
     */
    public function execute(Page $page, Revision $revision, ?string $author = null): bool
    {
        $content = $this->read->execute($revision);

        if ($content->toString() === $page->content->toString()) {
            return false;
        }

        $this->update->execute(
            $page,
            $page->slug,
            $page->title,
            $content,
            $author,
            $page->distribution_id,
            "Reverted to revision {$revision->shortSha()}",
        );

        return true;
    }
}

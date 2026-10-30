<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\PageKind;
use App\Features\Wiki\ValueObjects\Slug;
use App\Features\Wiki\ValueObjects\Title;
use App\Shared\Services\GitService;
use Illuminate\Support\Facades\Log;

final class UpdatePage
{
    public function __construct(
        private GitService $git,
        private RecordRevision $recordRevision,
    ) {}

    public function execute(Page $page, Slug $newSlug, Title $newTitle, Content $newContent, ?string $newAuthor = null, ?int $distributionId = null, ?string $summary = null, ?int $userId = null, PageKind $kind = PageKind::Article): Page
    {
        if ($newSlug->toString() !== $page->slug->toString()) {
            if (Page::where('slug', $newSlug->toString())->where('id', '!=', $page->id)->exists()) {
                throw new \RuntimeException("Page with slug '{$newSlug}' already exists.");
            }
        }

        $oldSlug = $page->slug->toString();

        $page->slug = $newSlug;
        $page->title = $newTitle;
        $page->content = $newContent;
        // The page keeps its first author; each revision records whoever made that edit.
        $editorName = $newAuthor ?? $page->author;
        $editorId = $newAuthor !== null ? $userId : $page->user_id;
        if ($page->author === null) {
            $page->author = $newAuthor;
            $page->user_id = $userId;
        }
        $page->distribution_id = $distributionId;
        $page->kind = $kind;
        $page->save();

        if ($oldSlug !== $newSlug->toString()) {
            $this->git->moveFile(
                'pages/'.$oldSlug.'.md',
                'pages/'.$newSlug->toString().'.md'
            );
        }

        $sha = $this->git->commitFile(
            $page->getMarkdownFilePath(),
            $newContent->toString(),
            "docs(wiki): update page {$page->slug}"
        );

        // No commit means the text did not change (for example only the distribution link).
        if ($sha !== null) {
            $this->recordRevision->execute($page, $sha, $editorName, $summary, $editorId);
        }

        Log::info('Wiki page updated', [
            'old_slug' => $oldSlug,
            'new_slug' => $page->slug->toString(),
            'author' => $newAuthor,
            'distribution_id' => $distributionId,
            'page_id' => $page->id,
        ]);

        return $page;
    }
}

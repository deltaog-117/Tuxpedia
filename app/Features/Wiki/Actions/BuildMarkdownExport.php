<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\MarkdownExport;

final class BuildMarkdownExport
{
    /**
     * Turn a version of a page into a standalone Markdown file.
     *
     * The stored text has no title, so one is added to make the file readable
     * on its own. A revision export is named after the revision's short sha.
     */
    public function execute(Page $page, Content $content, ?Revision $revision = null): MarkdownExport
    {
        $slug = $page->slug->toString();
        $text = rtrim($content->toString());
        $body = str_starts_with($text, '# ') ? $text : "# {$page->title}\n\n{$text}";

        return new MarkdownExport(
            $revision === null ? "{$slug}.md" : "{$slug}-{$revision->shortSha()}.md",
            rtrim($body)."\n",
        );
    }
}

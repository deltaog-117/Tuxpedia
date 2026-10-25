<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\ValueObjects\Content;

final class RenderContent
{
    /**
     * Render Markdown to HTML, with [[...]] links marked as existing or missing.
     */
    public function execute(Content $content): string
    {
        $slugs = $content->linkedSlugs();
        $known = $slugs === [] ? [] : array_fill_keys(Page::whereIn('slug', $slugs)->pluck('slug')->all(), true);

        return $content->toHtml($known);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\ArticleFile;
use App\Features\Wiki\ValueObjects\PageKind;
use PHPUnit\Framework\TestCase;

/**
 * The glossary folder holds concept and disambiguation pages. They must parse, never
 * collide with an article slug, and stay inside what the Markdown sanitiser keeps.
 */
final class GlossaryContentTest extends TestCase
{
    private const GLOSSARY_GLOB = __DIR__.'/../../../../database/content/glossary/*.md';

    private const ARTICLE_GLOB = __DIR__.'/../../../../database/content/wiki/*.md';

    public function test_every_glossary_page_parses_with_a_non_article_kind(): void
    {
        $files = glob(self::GLOSSARY_GLOB) ?: [];
        $this->assertNotEmpty($files);

        $articleSlugs = array_map(static fn (string $path): string => pathinfo($path, PATHINFO_FILENAME), glob(self::ARTICLE_GLOB) ?: []);

        $seen = [];
        foreach ($files as $path) {
            $page = ArticleFile::parse(basename($path), (string) file_get_contents($path));

            $this->assertNotSame(PageKind::Article, $page->kind, basename($path));
            $this->assertNotContains($page->slug->toString(), $articleSlugs, basename($path));
            $this->assertNotContains($page->slug->toString(), $seen, basename($path));
            $seen[] = $page->slug->toString();
            $this->assertDoesNotMatchRegularExpression('/<\/?[a-z][^>]*>/i', $page->content->toString(), basename($path));
            $this->assertStringNotContainsString('deltaog', strtolower($page->author.$page->content->toString()), basename($path));
        }
    }

    public function test_a_disambiguation_page_lists_at_least_two_targets(): void
    {
        foreach (glob(self::GLOSSARY_GLOB) ?: [] as $path) {
            $page = ArticleFile::parse(basename($path), (string) file_get_contents($path));

            if ($page->kind === PageKind::Disambiguation) {
                $this->assertGreaterThanOrEqual(2, substr_count($page->content->toString(), "\n- "), basename($path));
            }
        }
    }
}

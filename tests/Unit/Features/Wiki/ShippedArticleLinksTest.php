<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use PHPUnit\Framework\TestCase;

/**
 * The shipped articles link to each other, so a rename or a typo must not
 * leave a red link behind.
 */
final class ShippedArticleLinksTest extends TestCase
{
    private const CONTENT_GLOB = __DIR__.'/../../../../database/content/wiki/*.md';

    public function test_every_wiki_link_points_to_a_shipped_article(): void
    {
        $files = glob(self::CONTENT_GLOB) ?: [];
        $slugs = array_map(static fn (string $path): string => pathinfo($path, PATHINFO_FILENAME), $files);

        $broken = [];
        foreach ($files as $path) {
            preg_match_all('/\[\[([^\[\]|\n]+)(?:\|[^\[\]\n]*)?\]\]/', (string) file_get_contents($path), $matches);

            foreach ($matches[1] as $target) {
                if (! in_array(trim($target), $slugs, true)) {
                    $broken[] = basename($path).' -> '.$target;
                }
            }
        }

        $this->assertSame([], $broken, 'Links to articles that do not exist.');
    }

    public function test_articles_link_to_each_other(): void
    {
        $linked = array_filter(
            glob(self::CONTENT_GLOB) ?: [],
            static fn (string $path): bool => str_contains((string) file_get_contents($path), '[['),
        );

        $this->assertNotEmpty($linked);
    }
}

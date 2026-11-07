<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

/**
 * The "On this page" list: the h2 and h3 headings of a rendered article, in order.
 *
 * It reads the HTML that Content::toHtml() produced, whose headings already carry
 * unique ids, so the links here and the anchors in the article cannot drift apart.
 */
final class TableOfContents
{
    /** Fewer headings than this and a contents list adds nothing. */
    public const MIN_ENTRIES = 2;

    /** @param list<TocEntry> $entries */
    private function __construct(private readonly array $entries) {}

    public static function fromHtml(string $html): self
    {
        preg_match_all('#<h([23]) id="([^"]+)"[^>]*>(.*?)</h\1>#s', $html, $matches, PREG_SET_ORDER);

        $entries = [];

        foreach ($matches as [, $level, $id, $inner]) {
            $text = trim((string) preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5)));

            if ($text !== '') {
                $entries[] = new TocEntry((int) $level, html_entity_decode($id, ENT_QUOTES | ENT_HTML5), $text);
            }
        }

        return new self($entries);
    }

    /** @return list<TocEntry> */
    public function entries(): array
    {
        return $this->entries;
    }

    public function isWorthShowing(): bool
    {
        return count($this->entries) >= self::MIN_ENTRIES;
    }
}

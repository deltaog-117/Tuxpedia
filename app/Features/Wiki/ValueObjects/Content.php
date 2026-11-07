<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use App\Features\Wiki\Markdown\WikiLinkExtension;
use App\Features\Wiki\Markdown\WikiLinkParser;
use Illuminate\Support\Str;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

final class Content
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        // Allow empty content (empty page is okay)
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Render the Markdown as HTML that is safe to print unescaped.
     *
     * Wiki pages are user-authored, so raw HTML is stripped and unsafe link
     * schemes (javascript:, data:, vbscript:) are dropped.
     */
    public function toHtml(?array $knownSlugs = null): string
    {
        return $this->render(new WikiLinkParser($knownSlugs));
    }

    /**
     * Slugs of the pages this content links to with [[...]], existing or not.
     *
     * @return list<string>
     */
    public function linkedSlugs(): array
    {
        $parser = new WikiLinkParser;
        $this->render($parser);

        return $parser->foundSlugs();
    }

    private function render(WikiLinkParser $parser): string
    {
        return Str::markdown($this->value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            // Ids on h2/h3 are what the table of contents links to; no visible anchor is added.
            'heading_permalink' => [
                'min_heading_level' => 2,
                'max_heading_level' => 3,
                'insert' => 'none',
                'apply_id_to_heading' => true,
                'id_prefix' => '',
            ],
        ], [new WikiLinkExtension($parser), new FootnoteExtension, new HeadingPermalinkExtension]);
    }

    /**
     * Plain-text summary: the first paragraph without markup, cut at a word boundary.
     */
    public function toExcerpt(int $limit = 220): string
    {
        if (preg_match('#<p>(.*?)</p>#s', $this->toHtml(), $match) !== 1) {
            return '';
        }

        $text = html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5);

        return Str::limit(trim((string) preg_replace('/\s+/', ' ', $text)), $limit, '…', true);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

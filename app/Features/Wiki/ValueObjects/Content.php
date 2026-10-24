<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use Illuminate\Support\Str;

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
    public function toHtml(): string
    {
        return Str::markdown($this->value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
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

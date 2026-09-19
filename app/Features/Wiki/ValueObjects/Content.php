<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

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

    public function __toString(): string
    {
        return $this->value;
    }
}

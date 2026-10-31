<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

/**
 * A page version ready to download as a Markdown file.
 */
final readonly class MarkdownExport
{
    public function __construct(
        public string $filename,
        public string $body,
    ) {}
}

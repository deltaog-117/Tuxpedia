<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

final class TocEntry
{
    public function __construct(
        public readonly int $level,
        public readonly string $id,
        public readonly string $text,
    ) {}

    public function isSub(): bool
    {
        return $this->level === 3;
    }
}

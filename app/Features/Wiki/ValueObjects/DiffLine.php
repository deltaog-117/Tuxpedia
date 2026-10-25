<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

final readonly class DiffLine
{
    public function __construct(
        public DiffKind $kind,
        public string $text,
    ) {}
}

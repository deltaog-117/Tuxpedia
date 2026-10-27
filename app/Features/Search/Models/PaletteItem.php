<?php

declare(strict_types=1);

namespace App\Features\Search\Models;

/**
 * One row of the command palette: either a link (`url`) or a client-side
 * action (`action`, e.g. "theme"). Exactly one of the two is set.
 */
final readonly class PaletteItem
{
    private function __construct(
        public string $label,
        public string $group,
        public ?string $url,
        public ?string $action,
    ) {}

    public static function link(string $label, string $group, string $url): self
    {
        return new self($label, $group, $url, null);
    }

    public static function action(string $label, string $group, string $action): self
    {
        return new self($label, $group, null, $action);
    }
}

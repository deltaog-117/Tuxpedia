<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\ValueObjects;

use InvalidArgumentException;

/**
 * A distro's brand colour, always a validated `#rrggbb` hex string.
 *
 * Because the only way to obtain one is fromString(), the value can be placed
 * in an inline style or an SVG attribute without further escaping.
 */
final class Accent
{
    private function __construct(private readonly string $hex)
    {
    }

    public static function fromString(string $value): self
    {
        if (preg_match('/^#[0-9a-fA-F]{6}$/D', $value) !== 1) {
            throw new InvalidArgumentException("Accent must be a #rrggbb hex colour, got '{$value}'.");
        }

        return new self(strtolower($value));
    }

    /**
     * Space-separated channels, the format the theme's `rgb(var(--x) / alpha)` tokens expect.
     */
    public function toRgbChannels(): string
    {
        return implode(' ', array_map('hexdec', str_split(substr($this->hex, 1), 2)));
    }

    public function toString(): string
    {
        return $this->hex;
    }

    public function __toString(): string
    {
        return $this->hex;
    }
}

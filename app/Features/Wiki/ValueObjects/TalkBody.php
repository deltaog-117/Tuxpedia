<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use InvalidArgumentException;

/**
 * Plain text of a talk post. It is never parsed as Markdown or HTML; views print it escaped.
 */
final class TalkBody
{
    public const MAX_LENGTH = 5000;

    private function __construct(private readonly string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('A message cannot be empty.');
        }
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('A message cannot exceed ' . self::MAX_LENGTH . ' characters.');
        }
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}

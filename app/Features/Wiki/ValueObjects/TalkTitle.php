<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use InvalidArgumentException;

final class TalkTitle
{
    public const MAX_LENGTH = 150;

    private function __construct(private readonly string $value) {}

    public static function fromString(string $value): self
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('A topic needs a title.');
        }
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('A topic title cannot exceed '.self::MAX_LENGTH.' characters.');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}

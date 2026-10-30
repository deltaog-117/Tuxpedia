<?php

declare(strict_types=1);

namespace App\Features\Auth\ValueObjects;

use App\Features\Auth\Exceptions\InvalidUsernameException;

/**
 * The permanent handle in a profile URL; unlike the display name it is unique and never changes.
 */
final class Username
{
    public const MIN_LENGTH = 3;

    public const MAX_LENGTH = 30;

    // Starts and ends with a letter or digit, so a handle never looks like a flag or a dotfile.
    public const PATTERN = '/^[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?$/';

    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        $value = trim($value);
        $length = strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidUsernameException(
                sprintf('Username must be %d to %d characters.', self::MIN_LENGTH, self::MAX_LENGTH)
            );
        }
        if (! preg_match(self::PATTERN, $value)) {
            throw new InvalidUsernameException(
                'Username may only use lowercase letters, digits, hyphens and underscores, and must start and end with a letter or digit.'
            );
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

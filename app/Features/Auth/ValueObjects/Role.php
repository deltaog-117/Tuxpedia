<?php

declare(strict_types=1);

namespace App\Features\Auth\ValueObjects;

/**
 * The only roles an account can hold; a fourth value cannot be stored or cast.
 */
enum Role: string
{
    case Reader = 'reader';
    case Editor = 'editor';
    case Admin = 'admin';

    /** Each role includes the rights of the ones below it. */
    public function atLeast(self $minimum): bool
    {
        return $this->rank() >= $minimum->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::Reader => 0,
            self::Editor => 1,
            self::Admin => 2,
        };
    }
}

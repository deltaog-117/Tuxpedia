<?php

declare(strict_types=1);

namespace App\Features\Auth\Exceptions;

use InvalidArgumentException;

final class InvalidAvatarException extends InvalidArgumentException
{
    public static function tooLarge(int $limit): self
    {
        return new self("The picture is larger than {$limit} bytes.");
    }

    public static function unsupportedType(): self
    {
        return new self('The picture must be a JPEG or WebP image.');
    }

    public static function malformed(): self
    {
        return new self('The picture is not a clean JPEG or WebP file; let the page resize it before uploading.');
    }

    public static function badDimensions(int $min, int $max): self
    {
        return new self("The picture must be square, between {$min} and {$max} pixels wide.");
    }
}

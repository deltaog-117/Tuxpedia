<?php

declare(strict_types=1);

namespace App\Features\Auth\ValueObjects;

use App\Features\Auth\Exceptions\InvalidAvatarException;

/**
 * A profile picture that has passed every check; the only way to get one is fromBytes().
 *
 * The page shrinks and re-encodes the picture in the browser (the server has no GD or
 * Imagick), but a client can skip that step, so the server re-checks the file: type
 * from its bytes, size, square dimensions and a structure with nothing before or after
 * the image data, which rejects the usual "image plus hidden payload" files.
 */
final class AvatarImage
{
    public const MAX_BYTES = 200_000;

    public const MIN_SIDE = 32;

    public const MAX_SIDE = 512;

    private function __construct(
        private readonly string $bytes,
        private readonly string $mime,
    ) {}

    /**
     * @throws InvalidAvatarException
     */
    public static function fromBytes(string $bytes): self
    {
        if (strlen($bytes) > self::MAX_BYTES) {
            throw InvalidAvatarException::tooLarge(self::MAX_BYTES);
        }

        $mime = self::detectMime($bytes);

        if (! self::isCleanContainer($bytes, $mime)) {
            throw InvalidAvatarException::malformed();
        }

        $size = getimagesizefromstring($bytes);

        if ($size === false) {
            throw InvalidAvatarException::malformed();
        }

        [$width, $height] = $size;

        if ($width !== $height || $width < self::MIN_SIDE || $width > self::MAX_SIDE) {
            throw InvalidAvatarException::badDimensions(self::MIN_SIDE, self::MAX_SIDE);
        }

        return new self($bytes, $mime);
    }

    public function bytes(): string
    {
        return $this->bytes;
    }

    public function mime(): string
    {
        return $this->mime;
    }

    public function extension(): string
    {
        return $this->mime === 'image/webp' ? 'webp' : 'jpg';
    }

    /**
     * Read the type from the bytes themselves; the upload's claimed name and type are ignored.
     */
    private static function detectMime(string $bytes): string
    {
        $detected = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);

        if ($detected !== 'image/jpeg' && $detected !== 'image/webp') {
            throw InvalidAvatarException::unsupportedType();
        }

        return $detected;
    }

    private static function isCleanContainer(string $bytes, string $mime): bool
    {
        if ($mime === 'image/jpeg') {
            return str_starts_with($bytes, "\xFF\xD8\xFF") && str_ends_with($bytes, "\xFF\xD9");
        }

        // A WebP file is "RIFF", a 4-byte length, "WEBP"; the length must cover the whole file.
        if (! str_starts_with($bytes, 'RIFF') || substr($bytes, 8, 4) !== 'WEBP') {
            return false;
        }

        $declared = unpack('V', substr($bytes, 4, 4));

        return $declared !== false && strlen($bytes) === $declared[1] + 8;
    }
}

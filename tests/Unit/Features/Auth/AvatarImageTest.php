<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Auth;

use App\Features\Auth\Exceptions\InvalidAvatarException;
use App\Features\Auth\ValueObjects\AvatarImage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AvatarImageTest extends TestCase
{
    private function fixture(string $name): string
    {
        return (string) file_get_contents(dirname(__DIR__, 3).'/Fixtures/avatars/'.$name);
    }

    public function test_a_square_jpeg_is_accepted(): void
    {
        $image = AvatarImage::fromBytes($this->fixture('square.jpg'));

        $this->assertSame('image/jpeg', $image->mime());
        $this->assertSame('jpg', $image->extension());
    }

    public function test_a_square_webp_is_accepted(): void
    {
        $image = AvatarImage::fromBytes($this->fixture('square.webp'));

        $this->assertSame('image/webp', $image->mime());
        $this->assertSame('webp', $image->extension());
    }

    public function test_a_non_square_picture_is_rejected(): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes($this->fixture('wide.jpg'));
    }

    public function test_a_picture_over_the_size_limit_is_rejected(): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes($this->fixture('square.jpg').str_repeat('0', AvatarImage::MAX_BYTES));
    }

    /** @return array<string, array{string}> */
    public static function notImages(): array
    {
        return [
            'php script' => ['<?php system($_GET["c"]);'],
            'svg with script' => ['<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
            'html' => ['<html><body>hi</body></html>'],
            'empty' => [''],
            'gif header' => ["GIF89a\x01\x00\x01\x00\x00\x00\x00;"],
        ];
    }

    #[DataProvider('notImages')]
    public function test_files_that_are_not_jpeg_or_webp_are_rejected(string $bytes): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes($bytes);
    }

    public function test_a_jpeg_with_a_payload_appended_is_rejected(): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes($this->fixture('square.jpg').'<?php evil(); ?>');
    }

    public function test_a_webp_with_a_payload_appended_is_rejected(): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes($this->fixture('square.webp').'<?php evil(); ?>');
    }

    public function test_a_truncated_jpeg_is_rejected(): void
    {
        $this->expectException(InvalidAvatarException::class);

        AvatarImage::fromBytes(substr($this->fixture('square.jpg'), 0, -2));
    }

    public function test_corrupting_any_byte_never_yields_a_wrong_kind_of_file(): void
    {
        mt_srand(117);
        $original = $this->fixture('square.webp');

        for ($round = 0; $round < 300; $round++) {
            $bytes = $original;
            $bytes[mt_rand(0, strlen($bytes) - 1)] = chr(mt_rand(0, 255));

            try {
                $image = AvatarImage::fromBytes($bytes);
                $this->assertContains($image->mime(), ['image/jpeg', 'image/webp']);
            } catch (InvalidAvatarException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}

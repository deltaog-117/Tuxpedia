<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Auth;

use App\Features\Auth\Exceptions\InvalidUsernameException;
use App\Features\Auth\ValueObjects\Username;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UsernameTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function validHandles(): array
    {
        return [
            'letters' => ['alex'],
            'digits first' => ['9lives'],
            'inner hyphen and underscore' => ['mara-voss_2'],
            'shortest' => ['abc'],
            'longest' => [str_repeat('a', 30)],
        ];
    }

    /** @return array<string, array{string}> */
    public static function invalidHandles(): array
    {
        return [
            'empty' => [''],
            'too short' => ['ab'],
            'too long' => [str_repeat('a', 31)],
            'uppercase' => ['Alex'],
            'space' => ['mara voss'],
            'slash' => ['a/b'],
            'dot' => ['a.b'],
            'leading hyphen' => ['-alex'],
            'trailing underscore' => ['alex_'],
            'non-ascii' => ['álex'],
            'newline' => ["alex\nbob"],
        ];
    }

    #[DataProvider('validHandles')]
    public function test_accepts_a_valid_handle(string $handle): void
    {
        $this->assertSame($handle, Username::fromString($handle)->toString());
    }

    #[DataProvider('invalidHandles')]
    public function test_rejects_an_invalid_handle(string $handle): void
    {
        $this->expectException(InvalidUsernameException::class);

        Username::fromString($handle);
    }

    public function test_surrounding_whitespace_is_trimmed_before_checking(): void
    {
        $this->assertSame('alex', (string) Username::fromString("  alex \n"));
    }

    public function test_every_random_string_is_either_rejected_or_matches_the_pattern(): void
    {
        $alphabet = 'abcXYZ019-_ ./é';

        for ($i = 0; $i < 500; $i++) {
            $candidate = '';
            for ($n = random_int(0, 34); $n > 0; $n--) {
                $candidate .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            try {
                $handle = Username::fromString($candidate)->toString();
            } catch (InvalidUsernameException) {
                continue;
            }

            $this->assertMatchesRegularExpression(Username::PATTERN, $handle);
            $this->assertGreaterThanOrEqual(Username::MIN_LENGTH, strlen($handle));
            $this->assertLessThanOrEqual(Username::MAX_LENGTH, strlen($handle));
        }
    }
}

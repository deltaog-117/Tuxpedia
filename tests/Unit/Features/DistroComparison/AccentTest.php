<?php

declare(strict_types=1);

namespace Tests\Unit\Features\DistroComparison;

use App\Features\DistroComparison\ValueObjects\Accent;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AccentTest extends TestCase
{
    public function test_every_valid_hex_round_trips_and_yields_three_channels(): void
    {
        for ($i = 0; $i < 500; $i++) {
            $hex = sprintf('#%06x', random_int(0, 0xFFFFFF));

            $accent = Accent::fromString(strtoupper($hex));

            $this->assertSame($hex, $accent->toString());
            $channels = array_map('intval', explode(' ', $accent->toRgbChannels()));
            $this->assertCount(3, $channels);
            foreach ($channels as $channel) {
                $this->assertGreaterThanOrEqual(0, $channel);
                $this->assertLessThanOrEqual(255, $channel);
            }
        }
    }

    public function test_converts_known_colour_to_channels(): void
    {
        $this->assertSame('233 84 32', Accent::fromString('#e95420')->toRgbChannels());
    }

    #[DataProvider('invalidProvider')]
    public function test_rejects_anything_that_is_not_a_six_digit_hex(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Accent::fromString($value);
    }

    public static function invalidProvider(): array
    {
        return [
            'empty' => [''],
            'no hash' => ['e95420'],
            'short form' => ['#fff'],
            'too long' => ['#e954201'],
            'not hex' => ['#gggggg'],
            'css injection' => ['#e95420; background: url(x)'],
            'trailing newline' => ["#e95420\n"],
        ];
    }
}

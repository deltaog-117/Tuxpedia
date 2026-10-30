<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Auth;

use App\Features\Auth\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public static function names(): array
    {
        return [
            'two words' => ['Mara Voss', 'MV'],
            'one word' => ['editor', 'E'],
            'three words uses the first two' => ['Ada King Lovelace', 'AK'],
            'extra spaces and punctuation' => ['  mara.  voss ', 'MV'],
            'accented' => ['élodie Çelik', 'ÉÇ'],
            'no letters at all' => ['!!!', '?'],
        ];
    }

    #[DataProvider('names')]
    public function test_initials(string $name, string $expected): void
    {
        $this->assertSame($expected, (new User(['name' => $name]))->initials());
    }

    public function test_hue_is_stable_and_within_a_colour_wheel(): void
    {
        foreach (['alex', 'mara-voss', 'editor', 'x9'] as $handle) {
            $hue = (new User(['username' => $handle]))->avatarHue();

            $this->assertSame($hue, (new User(['username' => $handle]))->avatarHue());
            $this->assertGreaterThanOrEqual(0, $hue);
            $this->assertLessThan(360, $hue);
        }
    }
}

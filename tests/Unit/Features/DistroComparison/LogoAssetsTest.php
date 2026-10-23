<?php

declare(strict_types=1);

namespace Tests\Unit\Features\DistroComparison;

use PHPUnit\Framework\TestCase;

final class LogoAssetsTest extends TestCase
{
    public function test_every_bundled_logo_is_a_single_svg_without_scripts(): void
    {
        $files = glob(dirname(__DIR__, 4) . '/public/images/distros/*.svg');

        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $svg = (string) file_get_contents($file);

            $this->assertStringContainsString('<svg', $svg, basename($file));
            $this->assertMatchesRegularExpression('/<(path|polygon)\b/', $svg, basename($file));
            $this->assertStringNotContainsStringIgnoringCase('<script', $svg, basename($file));
        }
    }
}

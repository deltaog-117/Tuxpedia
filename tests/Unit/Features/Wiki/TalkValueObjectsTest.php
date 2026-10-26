<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\TalkBody;
use App\Features\Wiki\ValueObjects\TalkTitle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TalkValueObjectsTest extends TestCase
{
    public function test_a_title_is_trimmed(): void
    {
        $this->assertSame('Try NixOS', TalkTitle::fromString("  Try NixOS \n")->toString());
    }

    public function test_a_blank_title_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TalkTitle::fromString("  \t ");
    }

    public function test_a_title_over_the_limit_is_rejected(): void
    {
        $this->assertSame(TalkTitle::MAX_LENGTH, mb_strlen(TalkTitle::fromString(str_repeat('é', TalkTitle::MAX_LENGTH))->toString()));

        $this->expectException(InvalidArgumentException::class);
        TalkTitle::fromString(str_repeat('a', TalkTitle::MAX_LENGTH + 1));
    }

    public function test_a_body_keeps_its_line_breaks_and_markup_untouched(): void
    {
        $text = "Devuan is Debian without systemd.\n<b>not html</b>";

        $this->assertSame($text, TalkBody::fromString($text)->toString());
    }

    public function test_a_blank_body_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TalkBody::fromString("\n  ");
    }

    public function test_a_body_over_the_limit_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TalkBody::fromString(str_repeat('a', TalkBody::MAX_LENGTH + 1));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\PageKind;
use PHPUnit\Framework\TestCase;

final class PageKindTest extends TestCase
{
    public function test_every_kind_has_a_label_and_round_trips_through_its_value(): void
    {
        foreach (PageKind::cases() as $kind) {
            $this->assertNotSame('', $kind->label());
            $this->assertSame($kind, PageKind::from($kind->value));
        }
    }

    public function test_unknown_values_are_rejected(): void
    {
        $this->assertNull(PageKind::tryFrom('essay'));
    }
}

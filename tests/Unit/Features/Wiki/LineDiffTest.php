<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\DiffKind;
use App\Features\Wiki\ValueObjects\DiffLine;
use App\Features\Wiki\ValueObjects\LineDiff;
use PHPUnit\Framework\TestCase;

class LineDiffTest extends TestCase
{
    /**
     * @param  list<DiffLine>  $lines
     */
    private function rebuild(array $lines, DiffKind $skip): string
    {
        $kept = array_filter($lines, fn ($line): bool => $line->kind !== $skip);

        return implode("\n", array_map(fn ($line): string => $line->text, $kept));
    }

    public function test_marks_added_and_removed_lines(): void
    {
        $lines = LineDiff::between("a\nb\nc", "a\nx\nc");

        $this->assertSame(
            [['same', 'a'], ['removed', 'b'], ['added', 'x'], ['same', 'c']],
            array_map(fn ($l): array => [$l->kind->value, $l->text], $lines),
        );
    }

    public function test_identical_and_empty_inputs(): void
    {
        $this->assertSame([], LineDiff::between('', ''));
        $this->assertCount(2, LineDiff::between("a\nb", "a\nb"));
        $this->assertSame(DiffKind::Added, LineDiff::between('', 'new')[0]->kind);
        $this->assertSame(DiffKind::Removed, LineDiff::between('old', '')[0]->kind);
    }

    public function test_crlf_is_treated_as_lf(): void
    {
        $lines = LineDiff::between("a\r\nb", "a\nb");

        $this->assertSame([DiffKind::Same, DiffKind::Same], array_map(fn ($l): DiffKind => $l->kind, $lines));
    }

    public function test_rebuilds_both_sides_for_random_inputs(): void
    {
        mt_srand(117);
        $words = ['alpha', 'beta', 'gamma', '', 'delta'];

        for ($run = 0; $run < 200; $run++) {
            $old = implode("\n", array_map(fn () => $words[mt_rand(0, 4)], range(0, mt_rand(0, 12))));
            $new = implode("\n", array_map(fn () => $words[mt_rand(0, 4)], range(0, mt_rand(0, 12))));

            $lines = LineDiff::between($old, $new);

            $this->assertSame($old, $this->rebuild($lines, DiffKind::Added), "old side, run {$run}");
            $this->assertSame($new, $this->rebuild($lines, DiffKind::Removed), "new side, run {$run}");
        }
    }
}

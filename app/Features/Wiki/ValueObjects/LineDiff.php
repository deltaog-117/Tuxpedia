<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

/**
 * Line-by-line difference between two texts (longest common subsequence).
 *
 * The result always satisfies: Same + Removed lines rebuild the old text, and
 * Same + Added lines rebuild the new one (line endings normalised to "\n").
 */
final class LineDiff
{
    // Past this many table cells the middle is shown as one replaced block.
    private const MAX_CELLS = 1_000_000;

    /**
     * @return list<DiffLine>
     */
    public static function between(string $old, string $new): array
    {
        $a = self::lines($old);
        $b = self::lines($new);
        $n = count($a);
        $m = count($b);

        $head = 0;
        while ($head < $n && $head < $m && $a[$head] === $b[$head]) {
            $head++;
        }
        $endA = $n;
        $endB = $m;
        while ($endA > $head && $endB > $head && $a[$endA - 1] === $b[$endB - 1]) {
            $endA--;
            $endB--;
        }

        $result = [];
        for ($i = 0; $i < $head; $i++) {
            $result[] = new DiffLine(DiffKind::Same, $a[$i]);
        }
        foreach (self::middle(array_slice($a, $head, $endA - $head), array_slice($b, $head, $endB - $head)) as $line) {
            $result[] = $line;
        }
        for ($i = $endA; $i < $n; $i++) {
            $result[] = new DiffLine(DiffKind::Same, $a[$i]);
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    private static function lines(string $text): array
    {
        return $text === '' ? [] : explode("\n", str_replace("\r\n", "\n", $text));
    }

    /**
     * @param list<string> $a
     * @param list<string> $b
     * @return list<DiffLine>
     */
    private static function middle(array $a, array $b): array
    {
        $n = count($a);
        $m = count($b);

        if ($n * $m > self::MAX_CELLS) {
            return [
                ...array_map(fn (string $t): DiffLine => new DiffLine(DiffKind::Removed, $t), $a),
                ...array_map(fn (string $t): DiffLine => new DiffLine(DiffKind::Added, $t), $b),
            ];
        }

        // $table[$i][$j] = length of the common subsequence of $a[$i..] and $b[$j..].
        $table = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
        for ($i = $n - 1; $i >= 0; $i--) {
            for ($j = $m - 1; $j >= 0; $j--) {
                $table[$i][$j] = $a[$i] === $b[$j]
                    ? $table[$i + 1][$j + 1] + 1
                    : max($table[$i + 1][$j], $table[$i][$j + 1]);
            }
        }

        $result = [];
        $i = 0;
        $j = 0;
        while ($i < $n && $j < $m) {
            if ($a[$i] === $b[$j]) {
                $result[] = new DiffLine(DiffKind::Same, $a[$i++]);
                $j++;
            } elseif ($table[$i + 1][$j] >= $table[$i][$j + 1]) {
                $result[] = new DiffLine(DiffKind::Removed, $a[$i++]);
            } else {
                $result[] = new DiffLine(DiffKind::Added, $b[$j++]);
            }
        }
        while ($i < $n) {
            $result[] = new DiffLine(DiffKind::Removed, $a[$i++]);
        }
        while ($j < $m) {
            $result[] = new DiffLine(DiffKind::Added, $b[$j++]);
        }

        return $result;
    }
}

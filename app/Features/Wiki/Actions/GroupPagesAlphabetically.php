<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GroupPagesAlphabetically
{
    public const OTHER = '#';

    /**
     * Group pages under the first letter of their title, A to Z, with titles that start
     * with anything else (digits, symbols) under "#" at the end.
     *
     * @param  Collection<int, Page>  $pages
     * @return array<string, list<Page>>
     */
    public function execute(Collection $pages): array
    {
        $sorted = $pages
            ->sortBy(fn (Page $page): string => mb_strtolower((string) $page->title))
            ->values();

        $groups = [];
        foreach ($sorted as $page) {
            $groups[$this->letter((string) $page->title)][] = $page;
        }

        uksort($groups, fn (string $a, string $b): int => match (true) {
            $a === self::OTHER => 1,
            $b === self::OTHER => -1,
            default => strcmp($a, $b),
        });

        return $groups;
    }

    private function letter(string $title): string
    {
        $first = strtoupper(substr(Str::ascii($title), 0, 1));

        return ctype_alpha($first) ? $first : self::OTHER;
    }
}

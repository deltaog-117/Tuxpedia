<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Illuminate\Support\Collection;

class GroupPagesByFamily
{
    private const UNGROUPED = 'Other';

    private const STANDALONE = 'Independent';

    /**
     * Group pages by the family of their distribution ("Debian", "Arch", ...).
     *
     * Qualifiers such as "Ubuntu (LTS)" fold into their base name. Families are
     * alphabetical, with the standalone and ungrouped buckets last.
     *
     * @param  Collection<int, Page>  $pages
     * @return Collection<string, Collection<int, Page>>
     */
    public function execute(Collection $pages): Collection
    {
        return $pages
            ->sortBy(fn (Page $page): string => (string) $page->title)
            ->groupBy(fn (Page $page): string => $this->familyOf($page))
            ->sortBy(fn (Collection $group, string $family): string => match ($family) {
                self::UNGROUPED => '2',
                self::STANDALONE => '1',
                default => '0'.$family,
            });
    }

    private function familyOf(Page $page): string
    {
        $basedOn = $page->distribution?->based_on;

        if ($basedOn === null || trim($basedOn) === '') {
            return self::UNGROUPED;
        }

        return trim((string) preg_replace('/\s*\(.*\)$/', '', $basedOn));
    }
}

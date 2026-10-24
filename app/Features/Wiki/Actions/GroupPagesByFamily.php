<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Models\Page;
use Illuminate\Support\Collection;

class GroupPagesByFamily
{
    private const UNGROUPED = 'Other';

    private const STANDALONE = 'Independent';

    /**
     * Group pages by the root ancestor of their distribution ("Debian", "Arch", ...).
     *
     * The `based_on` chain is followed to its end, so Linux Mint lands beside
     * Ubuntu under Debian. Qualifiers such as "Ubuntu (LTS)" fold into their base
     * name. Roots with no derivatives share the standalone bucket. Families are
     * alphabetical, with the standalone and ungrouped buckets last.
     *
     * @param  Collection<int, Page>  $pages
     * @return Collection<string, Collection<int, Page>>
     */
    public function execute(Collection $pages): Collection
    {
        $known = $pages
            ->map(fn (Page $page): ?Distribution => $page->distribution)
            ->filter()
            ->keyBy(fn (Distribution $distribution): string => $this->key((string) $distribution->name));

        $roots = $pages->mapWithKeys(fn (Page $page, int $index): array => [$index => $this->rootOf($page, $known)]);
        $rootCounts = $roots->countBy();

        return $pages
            ->sortBy(fn (Page $page): string => (string) $page->title)
            ->groupBy(function (Page $page, int $index) use ($roots, $rootCounts): string {
                $root = $roots[$index];

                // A lone article that is its own root has no family to show.
                if ($rootCounts[$root] === 1 && $root === $page->distribution?->name) {
                    return self::STANDALONE;
                }

                return $root;
            })
            ->sortBy(fn (Collection $group, string $family): string => match ($family) {
                self::UNGROUPED => '2',
                self::STANDALONE => '1',
                default => '0'.$family,
            });
    }

    /**
     * @param  Collection<string, Distribution>  $known
     */
    private function rootOf(Page $page, Collection $known): string
    {
        $current = $page->distribution;

        if ($current === null || trim((string) $current->based_on) === '') {
            return self::UNGROUPED;
        }

        $seen = [$this->key((string) $current->name)];

        while (true) {
            $parent = trim((string) preg_replace('/\s*\(.*\)$/', '', (string) $current->based_on));
            $next = $known->get($this->key($parent));

            if ($parent === self::STANDALONE || $parent === '') {
                return (string) $current->name;
            }

            // A distribution with no article of its own still names the family.
            if ($next === null) {
                return $parent;
            }

            // A loop in the data must not hang the homepage.
            if (in_array($this->key((string) $next->name), $seen, true)) {
                return (string) $current->name;
            }

            $seen[] = $this->key((string) $next->name);
            $current = $next;
        }
    }

    /** "Arch" and "Arch Linux" must name the same distribution. */
    private function key(string $name): string
    {
        return (string) preg_replace('/\s+linux$/', '', mb_strtolower(trim($name)));
    }
}

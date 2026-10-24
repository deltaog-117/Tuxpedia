<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class GetFeaturedPage
{
    /**
     * Pick the featured article for a day: stable within the day, rotating through the library.
     *
     * Articles about a distribution are preferred; any page is used when none is linked.
     *
     * @param  Collection<int, Page>  $pages
     */
    public function execute(Collection $pages, CarbonInterface $day): ?Page
    {
        $candidates = $pages->filter(fn (Page $page): bool => $page->distribution !== null);

        if ($candidates->isEmpty()) {
            $candidates = $pages;
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        $ordered = $candidates->sortBy(fn (Page $page): string => (string) $page->slug)->values();

        return $ordered[$day->dayOfYear % $ordered->count()];
    }
}

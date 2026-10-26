<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Illuminate\Support\Collection;

final class ListRelatedPages
{
    public function __construct(private readonly ListBacklinks $listBacklinks) {}

    /**
     * Pages tied to the given one: those that link to it, those it links to and those in
     * one of its categories. Each appears once and the page itself is left out.
     *
     * @return Collection<int, Page>
     */
    public function execute(Page $page): Collection
    {
        $linkedTo = Page::whereIn('slug', function ($query) use ($page): void {
            $query->select('target_slug')->from('wiki_links')->where('page_id', $page->id);
        })->get();

        $categoryIds = $page->categories()->pluck('wiki_categories.id');
        $sharingCategory = $categoryIds->isEmpty()
            ? new Collection
            : Page::whereHas('categories', fn ($query) => $query->whereIn('wiki_categories.id', $categoryIds))->get();

        return $this->listBacklinks->execute($page)
            ->concat($linkedTo)
            ->concat($sharingCategory)
            ->reject(fn (Page $other): bool => $other->id === $page->id)
            ->unique('id')
            ->sortBy(fn (Page $other): string => mb_strtolower((string) $other->title))
            ->values();
    }
}

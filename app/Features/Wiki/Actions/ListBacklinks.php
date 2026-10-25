<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use Illuminate\Database\Eloquent\Collection;

final class ListBacklinks
{
    /**
     * Pages that link to the given page, by title. A page linking to itself is left out.
     *
     * @return Collection<int, Page>
     */
    public function execute(Page $page): Collection
    {
        return Page::whereIn('id', function ($query) use ($page): void {
            $query->select('page_id')->from('wiki_links')->where('target_slug', $page->slug->toString());
        })
            ->where('id', '!=', $page->id)
            ->orderBy('title')
            ->get();
    }
}

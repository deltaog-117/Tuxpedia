<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\PageLink;
use Illuminate\Support\Facades\DB;

final class SyncPageLinks
{
    /**
     * Make the stored links of a page match the [[...]] links in its content.
     */
    public function execute(Page $page): void
    {
        $slugs = $page->content->linkedSlugs();

        DB::transaction(function () use ($page, $slugs): void {
            PageLink::where('page_id', $page->id)->delete();

            foreach ($slugs as $slug) {
                PageLink::create(['page_id' => $page->id, 'target_slug' => $slug]);
            }
        });
    }
}

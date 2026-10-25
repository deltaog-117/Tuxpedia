<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\Wiki\Actions\GetRandomPage;
use App\Features\Wiki\Actions\GroupPagesAlphabetically;
use App\Features\Wiki\Actions\ListRecentChanges;
use App\Features\Wiki\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscoveryController
{
    public function random(GetRandomPage $getRandomPage): RedirectResponse
    {
        $slug = $getRandomPage->execute(Page::pluck('slug')->map(fn ($slug): string => (string) $slug));

        return $slug === null
            ? redirect()->route('wiki.index')
            : redirect()->route('wiki.show', $slug);
    }

    public function recent(ListRecentChanges $listRecentChanges): View
    {
        return view('features.wiki.recent', ['revisions' => $listRecentChanges->execute()]);
    }

    public function all(GroupPagesAlphabetically $group): View
    {
        $pages = Page::with('distribution')->get();

        return view('features.wiki.all', [
            'groups' => $group->execute($pages),
            'total' => $pages->count(),
        ]);
    }
}

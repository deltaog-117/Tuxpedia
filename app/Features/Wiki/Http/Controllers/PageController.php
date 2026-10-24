<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\GetFeaturedPage;
use App\Features\Wiki\Actions\GetPage;
use App\Features\Wiki\Actions\GroupPagesByFamily;
use App\Features\Wiki\Exceptions\PageNotFoundException;
use App\Features\Wiki\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController
{
    public function show(Request $request, string $slug, GetPage $getPage): View
    {
        try {
            $page = $getPage->execute($slug);

            return view('features.wiki.show', ['page' => $page]);
        } catch (PageNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function index(GetFeaturedPage $getFeaturedPage, GroupPagesByFamily $groupPagesByFamily): View
    {
        $pages = Page::with('distribution')->orderBy('title')->get();

        return view('features.wiki.index', [
            'pages' => $pages,
            'featured' => $getFeaturedPage->execute($pages, now()),
            'families' => $groupPagesByFamily->execute($pages),
            'distributionCount' => Distribution::count(),
        ]);
    }
}

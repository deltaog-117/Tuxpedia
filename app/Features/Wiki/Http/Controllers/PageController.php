<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\GetFeaturedPage;
use App\Features\Wiki\Actions\CompareRevisions;
use App\Features\Wiki\Actions\GetPage;
use App\Features\Wiki\Actions\GroupPagesByFamily;
use App\Features\Wiki\Actions\ReadRevision;
use App\Features\Wiki\Actions\RevertPage;
use App\Features\Wiki\Exceptions\PageNotFoundException;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;
use Illuminate\Http\RedirectResponse;
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

    public function history(string $slug, GetPage $getPage): View
    {
        $page = $this->findPage($slug, $getPage);

        return view('features.wiki.history', [
            'page' => $page,
            'revisions' => $page->revisions()->get(),
        ]);
    }

    public function revision(string $slug, string $sha, GetPage $getPage, ReadRevision $read): View
    {
        $page = $this->findPage($slug, $getPage);
        $revision = $page->revisions()->where('sha', $sha)->firstOrFail();

        return view('features.wiki.revision', [
            'page' => $page,
            'revision' => $revision,
            'content' => $read->execute($revision),
            'isCurrent' => $page->revisions()->first()?->is($revision) ?? false,
        ]);
    }

    public function diff(Request $request, string $slug, GetPage $getPage, CompareRevisions $compare): View
    {
        $page = $this->findPage($slug, $getPage);
        $data = $request->validate([
            'from' => ['required', 'string', 'regex:/^[0-9a-f]{40}$/'],
            'to' => ['required', 'string', 'regex:/^[0-9a-f]{40}$/'],
        ]);

        // Only revisions that belong to this page are ever handed to Git.
        $from = $page->revisions()->where('sha', $data['from'])->firstOrFail();
        $to = $page->revisions()->where('sha', $data['to'])->firstOrFail();

        return view('features.wiki.diff', [
            'page' => $page,
            'from' => $from,
            'to' => $to,
            'lines' => $compare->execute($from, $to),
        ]);
    }

    public function revert(Request $request, string $slug, string $sha, GetPage $getPage, RevertPage $revert): RedirectResponse
    {
        $page = $this->findPage($slug, $getPage);
        $revision = $page->revisions()->where('sha', $sha)->firstOrFail();

        $changed = $revert->execute($page, $revision, $request->user()?->name);

        return redirect()
            ->route($changed ? 'wiki.show' : 'wiki.history', $page->slug->toString())
            ->with('message', $changed
                ? "Page restored to revision {$revision->shortSha()}."
                : 'The page already has that content.');
    }

    private function findPage(string $slug, GetPage $getPage): Page
    {
        try {
            return $getPage->execute($slug);
        } catch (PageNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }
}

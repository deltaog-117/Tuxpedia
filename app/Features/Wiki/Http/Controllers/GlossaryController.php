<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\Wiki\Models\Page;
use App\Features\Wiki\ValueObjects\PageKind;
use Illuminate\View\View;

class GlossaryController
{
    public function index(): View
    {
        return view('features.wiki.glossary', [
            'concepts' => Page::where('kind', PageKind::Concept->value)->orderBy('title')->get(),
            'ambiguous' => Page::where('kind', PageKind::Disambiguation->value)->orderBy('title')->get(),
        ]);
    }
}

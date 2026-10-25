<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\Wiki\Models\Category;
use Illuminate\View\View;

class CategoryController
{
    public function index(): View
    {
        // Categories left without pages are hidden rather than deleted.
        $categories = Category::withCount('pages')->has('pages')->orderBy('name')->get();

        return view('features.wiki.categories', ['categories' => $categories]);
    }

    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return view('features.wiki.category', [
            'category' => $category,
            'pages' => $category->pages()->with('distribution')->orderBy('title')->get(),
        ]);
    }
}

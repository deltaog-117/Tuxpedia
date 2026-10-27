<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Exceptions\PageNotFoundException;
use App\Features\Wiki\Models\Page;

final class GetPage
{
    public function execute(string $slug): Page
    {
        $page = Page::with('distribution')->where('slug', $slug)->first();
        if (! $page) {
            throw new PageNotFoundException($slug);
        }

        return $page;
    }
}

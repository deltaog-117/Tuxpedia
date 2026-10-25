<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Category;
use App\Features\Wiki\Models\Page;
use Illuminate\Support\Facades\DB;

final class SyncPageCategories
{
    public function __construct(private ParseCategoryNames $parse) {}

    /**
     * Make the categories of a page match a comma-separated list, creating the ones that do not exist yet.
     */
    public function execute(Page $page, string $input): void
    {
        $names = $this->parse->execute($input);

        DB::transaction(function () use ($page, $names): void {
            $ids = [];
            foreach ($names as $slug => $name) {
                $ids[] = Category::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
            }

            $page->categories()->sync($ids);
        });
    }
}

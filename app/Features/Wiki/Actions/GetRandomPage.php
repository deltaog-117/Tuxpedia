<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use Illuminate\Support\Collection;

class GetRandomPage
{
    /**
     * Pick the slug of one page at random, or null when the wiki is empty.
     *
     * @param  Collection<int, string>  $slugs
     */
    public function execute(Collection $slugs): ?string
    {
        if ($slugs->isEmpty()) {
            return null;
        }

        return $slugs->values()[random_int(0, $slugs->count() - 1)];
    }
}

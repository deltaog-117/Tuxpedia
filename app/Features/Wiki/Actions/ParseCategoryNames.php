<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use Illuminate\Support\Str;

final class ParseCategoryNames
{
    public const MAX_CATEGORIES = 20;

    /**
     * Split a comma-separated list into category names keyed by slug. Names that slug to the
     * same value ("Init System", "init-system") count once, and the first spelling wins.
     *
     * @return array<string, string>
     */
    public function execute(string $input): array
    {
        $categories = [];

        foreach (explode(',', $input) as $part) {
            $name = trim((string) preg_replace('/\s+/u', ' ', $part));
            $slug = Str::slug($name);

            if ($slug === '' || isset($categories[$slug])) {
                continue;
            }

            $categories[$slug] = mb_substr($name, 0, 255);

            if (count($categories) === self::MAX_CATEGORIES) {
                break;
            }
        }

        return $categories;
    }
}

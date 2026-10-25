<?php

declare(strict_types=1);

namespace App\Features\Search\Actions;

use App\Features\Search\Models\PaletteItem;
use App\Features\Wiki\Models\Page;

class FindPalettePages
{
    private const ESCAPE = '!';

    /**
     * Pages whose title contains the query; the most recently updated pages
     * when the query is blank.
     *
     * @return list<PaletteItem>
     */
    public function execute(string $query, int $limit = 6): array
    {
        $needle = trim($query);

        $pages = Page::query()->select(['slug', 'title'])->orderByDesc('updated_at')->limit($limit);
        if ($needle !== '') {
            $pages->orderBy('title')->whereRaw(
                'title LIKE ? ESCAPE \'' . self::ESCAPE . '\'',
                [self::likePattern($needle)]
            );
        }

        $items = [];
        foreach ($pages->get() as $page) {
            $items[] = PaletteItem::link((string) $page->title, 'Pages', route('wiki.show', (string) $page->slug));
        }

        return $items;
    }

    /**
     * A contains-pattern with the LIKE wildcards in the input made literal.
     * "!" is the escape character because, unlike a backslash, it means the
     * same thing in MySQL and SQLite string literals.
     */
    public static function likePattern(string $input): string
    {
        $escaped = str_replace(
            [self::ESCAPE, '%', '_'],
            [self::ESCAPE . self::ESCAPE, self::ESCAPE . '%', self::ESCAPE . '_'],
            $input
        );

        return '%' . $escaped . '%';
    }
}

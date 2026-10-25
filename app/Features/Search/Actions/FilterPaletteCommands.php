<?php

declare(strict_types=1);

namespace App\Features\Search\Actions;

use App\Features\Search\Models\PaletteItem;

class FilterPaletteCommands
{
    /**
     * @return list<PaletteItem>
     */
    public function execute(string $query, bool $authenticated): array
    {
        $needle = mb_strtolower(trim($query));

        $matches = [];
        foreach ($this->commands($authenticated) as [$keywords, $item]) {
            if ($needle === '' || str_contains(mb_strtolower($item->label . ' ' . $keywords), $needle)) {
                $matches[] = $item;
            }
        }

        return $matches;
    }

    /**
     * @return list<array{0: string, 1: PaletteItem}> keywords and item
     */
    private function commands(bool $authenticated): array
    {
        $commands = [
            ['home articles', PaletteItem::link('Wiki', 'Go to', route('wiki.index'))],
            ['lucky surprise', PaletteItem::link('Random article', 'Go to', route('wiki.random'))],
            ['changes latest history', PaletteItem::link('Recent changes', 'Go to', route('wiki.recent'))],
            ['index a-z list', PaletteItem::link('All pages', 'Go to', route('wiki.all'))],
            ['create write add', PaletteItem::link('New page', 'Go to', route('wiki.create'))],
            ['compare table', PaletteItem::link('Distros', 'Go to', route('distro-comparison.index'))],
            ['map graph parents', PaletteItem::link('Family tree', 'Go to', route('family-tree.index'))],
            ['shell console', PaletteItem::link('Terminal', 'Go to', route('terminal.index'))],
            ['dark light mode', PaletteItem::action('Toggle dark mode', 'Actions', 'theme')],
        ];

        if (! $authenticated) {
            $commands[] = ['sign in', PaletteItem::link('Login', 'Account', route('auth.login'))];
            $commands[] = ['sign up', PaletteItem::link('Register', 'Account', route('auth.register'))];
        }

        return $commands;
    }
}

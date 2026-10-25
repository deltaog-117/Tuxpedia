<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Search;

use App\Features\Search\Actions\FilterPaletteCommands;
use App\Features\Search\Actions\FindPalettePages;
use App\Features\Search\Models\PaletteItem;
use Tests\TestCase;

class PaletteTest extends TestCase
{
    /**
     * @param list<PaletteItem> $items
     * @return list<string>
     */
    private function labels(array $items): array
    {
        return array_map(static fn (PaletteItem $item): string => $item->label, $items);
    }

    public function test_blank_query_lists_every_command(): void
    {
        $labels = $this->labels((new FilterPaletteCommands())->execute('  ', true));

        $this->assertSame(['Wiki', 'New page', 'Distros', 'Family tree', 'Terminal', 'Toggle dark mode'], $labels);
    }

    public function test_query_matches_labels_and_keywords_case_insensitively(): void
    {
        $action = new FilterPaletteCommands();

        $this->assertSame(['Family tree'], $this->labels($action->execute('TREE', true)));
        $this->assertSame(['Toggle dark mode'], $this->labels($action->execute('dark', true)));
        $this->assertSame(['Terminal'], $this->labels($action->execute('shell', true)));
        $this->assertSame([], $action->execute('zzz', true));
    }

    public function test_account_commands_are_only_offered_to_guests(): void
    {
        $action = new FilterPaletteCommands();

        $this->assertSame(['Login'], $this->labels($action->execute('login', false)));
        $this->assertSame([], $action->execute('login', true));
    }

    public function test_theme_toggle_is_an_action_and_the_rest_are_links(): void
    {
        foreach ((new FilterPaletteCommands())->execute('', true) as $item) {
            $isTheme = $item->label === 'Toggle dark mode';
            $this->assertSame($isTheme ? 'theme' : null, $item->action);
            $this->assertSame($isTheme, $item->url === null);
        }
    }

    public function test_like_pattern_makes_wildcards_literal(): void
    {
        $this->assertSame('%arch%', FindPalettePages::likePattern('arch'));
        $this->assertSame('%100!%!_x%', FindPalettePages::likePattern('100%_x'));
        $this->assertSame('%a!!b%', FindPalettePages::likePattern('a!b'));
    }
}

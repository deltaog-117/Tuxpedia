<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\Actions\GetRandomPage;
use App\Features\Wiki\Actions\GroupPagesAlphabetically;
use App\Features\Wiki\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class DiscoveryActionsTest extends TestCase
{
    private function page(string $title): Page
    {
        return new Page(['slug' => Str::slug($title), 'title' => $title, 'content' => 'Body']);
    }

    public function test_random_page_is_null_for_an_empty_wiki_and_a_member_otherwise(): void
    {
        $action = new GetRandomPage;

        $this->assertNull($action->execute(new Collection));

        $slugs = new Collection(['arch', 'debian', 'fedora']);
        for ($i = 0; $i < 20; $i++) {
            $this->assertContains($action->execute($slugs), $slugs->all());
        }
    }

    public function test_random_page_can_reach_every_page(): void
    {
        $slugs = new Collection(['arch', 'debian', 'fedora']);
        $seen = collect(range(1, 200))->map(fn (): ?string => (new GetRandomPage)->execute($slugs))->unique();

        $this->assertCount(3, $seen);
    }

    public function test_pages_group_by_first_letter_with_other_titles_last(): void
    {
        $pages = new Collection([
            $this->page('Fedora'),
            $this->page('4MLinux'),
            $this->page('arch Linux'),
            $this->page('Alpine'),
            $this->page('Éclair'),
        ]);

        $groups = (new GroupPagesAlphabetically)->execute($pages);

        $this->assertSame(['A', 'E', 'F', '#'], array_keys($groups));
        $this->assertSame(['Alpine', 'arch Linux'], array_map(fn (Page $p): string => (string) $p->title, $groups['A']));
        $this->assertSame('4MLinux', (string) $groups['#'][0]->title);
    }

    public function test_grouping_an_empty_wiki_is_empty(): void
    {
        $this->assertSame([], (new GroupPagesAlphabetically)->execute(new Collection));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\GetFeaturedPage;
use App\Features\Wiki\Actions\GroupPagesByFamily;
use App\Features\Wiki\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HomepageActionsTest extends TestCase
{
    private function page(string $slug, ?string $basedOn): Page
    {
        $page = new Page(['slug' => $slug, 'title' => ucfirst($slug), 'content' => 'Body']);
        $page->setRelation('distribution', $basedOn === null ? null : new Distribution(['name' => ucfirst($slug), 'slug' => $slug, 'based_on' => $basedOn]));

        return $page;
    }

    public function test_featured_page_is_stable_within_a_day_and_rotates_across_days(): void
    {
        $pages = new Collection([$this->page('arch', 'Independent'), $this->page('debian', 'Independent'), $this->page('fedora', 'Red Hat')]);
        $action = new GetFeaturedPage();

        $day = CarbonImmutable::parse('2026-03-01');
        $this->assertSame($action->execute($pages, $day), $action->execute($pages, $day->setTime(18, 0)));

        $seen = collect(range(0, 2))
            ->map(fn (int $offset): string => (string) $action->execute($pages, $day->addDays($offset))->slug)
            ->unique();
        $this->assertCount(3, $seen);
    }

    public function test_featured_page_prefers_distribution_articles_and_handles_empty_library(): void
    {
        $action = new GetFeaturedPage();
        $day = CarbonImmutable::parse('2026-03-01');

        $this->assertNull($action->execute(new Collection(), $day));

        $pages = new Collection([$this->page('notes', null), $this->page('arch', 'Independent')]);
        $this->assertSame('arch', (string) $action->execute($pages, $day)->slug);
    }

    public function test_pages_group_by_root_ancestor_with_catch_alls_last(): void
    {
        $pages = new Collection([
            $this->page('notes', null),
            $this->page('nix', 'Independent'),
            $this->page('debian', 'Independent'),
            $this->page('ubuntu', 'Debian'),
            $this->page('mint', 'Ubuntu (LTS)'),
            $this->page('tails', 'Debian'),
            $this->page('fedora', 'Red Hat'),
            $this->page('arch', 'Independent'),
            $this->page('artix', 'Arch'),
            $this->page('suse', 'Independent (SUSE)'),
        ]);

        $families = (new GroupPagesByFamily())->execute($pages);

        $this->assertSame(['Arch', 'Debian', 'Red Hat', 'Independent', 'Other'], $families->keys()->all());
        $this->assertSame(
            ['Debian', 'Mint', 'Tails', 'Ubuntu'],
            $families['Debian']->map(fn (Page $p): string => (string) $p->title)->values()->all()
        );
        $this->assertSame(['Fedora'], $families['Red Hat']->map(fn (Page $p): string => (string) $p->title)->values()->all());
        $this->assertSame(['Nix', 'Suse'], $families['Independent']->map(fn (Page $p): string => (string) $p->title)->values()->all());
    }

    public function test_a_cycle_in_based_on_terminates(): void
    {
        $pages = new Collection([$this->page('a', 'B'), $this->page('b', 'A')]);

        $this->assertSame(2, (new GroupPagesByFamily())->execute($pages)->flatten()->count());
    }
}

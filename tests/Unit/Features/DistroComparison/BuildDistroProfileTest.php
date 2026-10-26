<?php

declare(strict_types=1);

namespace Tests\Unit\Features\DistroComparison;

use App\Features\DistroComparison\Actions\BuildDistroProfile;
use App\Features\DistroComparison\Models\Distribution;
use App\Features\DistroComparison\ValueObjects\DistroProfile;
use Illuminate\Support\Collection;
use Tests\TestCase;

final class BuildDistroProfileTest extends TestCase
{
    /**
     * @param  list<array{0: string, 1: ?string}>  $rows  name and `based_on`
     * @return Collection<int, Distribution>
     */
    private function distros(array $rows): Collection
    {
        return collect($rows)->map(fn (array $row): Distribution => new Distribution([
            'name' => $row[0],
            'slug' => strtolower(str_replace(' ', '-', $row[0])),
            'based_on' => $row[1],
        ]));
    }

    private function profile(Collection $distros, string $name): DistroProfile
    {
        return (new BuildDistroProfile)->execute($distros->firstWhere('name', $name), $distros);
    }

    /** @return list<string> */
    private function names(iterable $items): array
    {
        $names = [];
        foreach ($items as $item) {
            $names[] = is_array($item) ? $item['name'] : (string) $item->name;
        }

        return $names;
    }

    public function test_lineage_runs_from_the_root_to_the_parent_through_qualifiers(): void
    {
        $distros = $this->distros([
            ['Debian', 'Independent'],
            ['Ubuntu', 'Debian'],
            ['Linux Mint', 'Ubuntu (LTS)'],
        ]);

        $profile = $this->profile($distros, 'Linux Mint');

        $this->assertSame(['Debian', 'Ubuntu'], $this->names($profile->lineage));
        $this->assertSame('Ubuntu', $profile->lineage[1]['distribution']->name);
    }

    public function test_siblings_and_derivatives_are_alphabetical_and_exclude_the_distribution(): void
    {
        $distros = $this->distros([
            ['Debian', 'Independent'],
            ['Ubuntu', 'Debian'],
            ['Kali', 'Debian'],
            ['Devuan', 'Debian'],
            ['Linux Mint', 'Ubuntu'],
        ]);

        $ubuntu = $this->profile($distros, 'Ubuntu');
        $debian = $this->profile($distros, 'Debian');

        $this->assertSame(['Devuan', 'Kali'], $this->names($ubuntu->siblings));
        $this->assertSame(['Linux Mint'], $this->names($ubuntu->derivatives));
        $this->assertSame(['Devuan', 'Kali', 'Ubuntu'], $this->names($debian->derivatives));
        $this->assertSame([], $this->names($debian->siblings));
    }

    public function test_short_names_match_and_a_parent_without_a_row_stays_as_a_bare_name(): void
    {
        $distros = $this->distros([
            ['Arch Linux', 'Independent'],
            ['Artix Linux', 'Arch'],
            ['Fedora', 'Red Hat'],
            ['CentOS', 'Red Hat'],
        ]);

        $artix = $this->profile($distros, 'Artix Linux');
        $fedora = $this->profile($distros, 'Fedora');

        $this->assertSame(['Arch Linux'], $this->names($artix->lineage));
        $this->assertSame(['Red Hat'], $this->names($fedora->lineage));
        $this->assertNull($fedora->lineage[0]['distribution']);
        $this->assertSame(['CentOS'], $this->names($fedora->siblings));
    }

    public function test_independent_blank_and_self_based_distributions_have_no_family(): void
    {
        $distros = $this->distros([
            ['Gentoo', 'Independent'],
            ['Void', null],
            ['Alpine', 'Alpine'],
        ]);

        foreach (['Gentoo', 'Void', 'Alpine'] as $name) {
            $profile = $this->profile($distros, $name);

            $this->assertSame([], $profile->lineage, $name);
            $this->assertCount(0, $profile->siblings, $name);
            $this->assertCount(0, $profile->derivatives, $name);
        }
    }

    public function test_a_loop_in_the_data_ends_the_lineage(): void
    {
        $distros = $this->distros([['A', 'B'], ['B', 'C'], ['C', 'A']]);

        $this->assertSame(['C', 'B'], $this->names($this->profile($distros, 'A')->lineage));
    }

    public function test_invariants_hold_for_random_data(): void
    {
        mt_srand(117);

        for ($round = 0; $round < 200; $round++) {
            $count = mt_rand(1, 12);
            $rows = [];
            for ($i = 0; $i < $count; $i++) {
                $parent = match (mt_rand(0, 3)) {
                    0 => 'Independent',
                    1 => null,
                    default => 'D'.mt_rand(0, $count + 1),
                };
                $rows[] = ['D'.$i, $parent];
            }
            $distros = $this->distros($rows);

            foreach ($distros as $distro) {
                $profile = (new BuildDistroProfile)->execute($distro, $distros);
                $lineage = $this->names($profile->lineage);

                $this->assertNotContains($distro->name, $lineage);
                $this->assertSame($lineage, array_values(array_unique($lineage)));
                $this->assertNotContains($distro->name, $this->names($profile->siblings));
                $this->assertNotContains($distro->name, $this->names($profile->derivatives));
                $this->assertEmpty(array_intersect($this->names($profile->siblings), $this->names($profile->derivatives)));
            }
        }
    }
}

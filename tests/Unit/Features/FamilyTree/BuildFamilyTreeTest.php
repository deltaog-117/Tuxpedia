<?php

declare(strict_types=1);

namespace Tests\Unit\Features\FamilyTree;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\FamilyTree\Actions\BuildFamilyTree;
use Tests\TestCase;

final class BuildFamilyTreeTest extends TestCase
{
    private function tree(array $rows): array
    {
        $distros = collect($rows)->map(fn (array $row): Distribution => new Distribution([
            'name' => $row[0],
            'slug' => strtolower(str_replace(' ', '-', $row[0])),
            'based_on' => $row[1],
        ]));

        return (new BuildFamilyTree())->execute($distros);
    }

    private function names(array $nodes): array
    {
        return array_map(fn (array $node): string => $node['name'], $nodes);
    }

    public function test_nests_derivatives_through_qualifiers_and_short_names(): void
    {
        $tree = $this->tree([
            ['Debian', 'Independent'],
            ['Ubuntu', 'Debian'],
            ['Linux Mint', 'Ubuntu (LTS)'],
            ['Arch Linux', 'Independent'],
            ['Artix Linux', 'Arch'],
        ]);

        $this->assertSame(['Debian', 'Arch Linux'], $this->names($tree));
        $this->assertSame(['Ubuntu'], $this->names($tree[0]['children']));
        $this->assertSame(['Linux Mint'], $this->names($tree[0]['children'][0]['children']));
        $this->assertSame(['Artix Linux'], $this->names($tree[1]['children']));
    }

    public function test_a_parent_without_a_row_becomes_a_node(): void
    {
        $tree = $this->tree([['Fedora', 'Red Hat'], ['CentOS', 'Red Hat']]);

        $this->assertSame(['Red Hat'], $this->names($tree));
        $this->assertSame(['Fedora', 'CentOS'], $this->names($tree[0]['children']));
    }

    public function test_independent_qualifier_and_loops_make_roots(): void
    {
        $tree = $this->tree([['openSUSE', 'Independent (SUSE)'], ['A', 'B'], ['B', 'A']]);

        $this->assertEqualsCanonicalizing(['openSUSE', 'A'], $this->names($tree));
    }
}

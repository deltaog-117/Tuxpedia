<?php

declare(strict_types=1);

namespace Tests\Unit\Features\FamilyTree;

use App\Features\FamilyTree\Actions\BuildFamilyGraph;
use PHPUnit\Framework\TestCase;

final class BuildFamilyGraphTest extends TestCase
{
    private function node(string $name, array $children = [], ?string $page = null, ?string $logo = null): array
    {
        return ['name' => $name, 'slug' => strtolower($name), 'accent' => null, 'page' => $page, 'summary' => null, 'logo' => $logo, 'children' => $children];
    }

    public function test_flattens_families_into_clusters_with_links_and_counts(): void
    {
        $graph = (new BuildFamilyGraph)->execute([
            $this->node('Debian', [$this->node('Ubuntu', [$this->node('Mint')])]),
            $this->node('Gentoo'),
            $this->node('Void'),
        ], fn (string $slug): string => "/wiki/$slug");

        $this->assertSame([['id' => 0, 'name' => 'Debian', 'standalone' => false], ['id' => 1, 'name' => 'Independent', 'standalone' => true]], $graph['clusters']);
        $this->assertSame([['source' => 'debian', 'target' => 'ubuntu'], ['source' => 'ubuntu', 'target' => 'mint']], $graph['links']);

        $byId = array_column($graph['nodes'], null, 'id');
        $this->assertSame(2, $byId['debian']['derivatives']);
        $this->assertSame('debian', $byId['ubuntu']['parent']);
        $this->assertSame(2, $byId['mint']['depth']);
        $this->assertSame(1, $byId['gentoo']['cluster']);
    }

    public function test_links_a_node_to_its_article_only_when_it_has_one(): void
    {
        $graph = (new BuildFamilyGraph)->execute([$this->node('Debian', [$this->node('Ubuntu')], 'debian')], fn (string $slug): string => "/wiki/$slug");

        $byId = array_column($graph['nodes'], null, 'id');
        $this->assertSame('/wiki/debian', $byId['debian']['url']);
        $this->assertNull($byId['ubuntu']['url']);
    }

    public function test_has_no_independent_cluster_when_every_root_has_derivatives(): void
    {
        $graph = (new BuildFamilyGraph)->execute([$this->node('Debian', [$this->node('Ubuntu')])], fn (string $slug): string => $slug);

        $this->assertCount(1, $graph['clusters']);
    }

    public function test_flags_raster_logos_so_they_are_not_recoloured(): void
    {
        $graph = (new BuildFamilyGraph)->execute([
            $this->node('Debian', [$this->node('Ubuntu', [], null, '/images/distros/ubuntu.svg')], null, '/images/distros/tinycore.png'),
        ], fn (string $slug): string => $slug);

        $byId = array_column($graph['nodes'], null, 'id');
        $this->assertTrue($byId['debian']['raster']);
        $this->assertFalse($byId['ubuntu']['raster']);
    }
}

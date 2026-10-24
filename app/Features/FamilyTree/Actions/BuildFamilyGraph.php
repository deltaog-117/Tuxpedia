<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Actions;

class BuildFamilyGraph
{
    public const STANDALONE = 'Independent';

    /**
     * Flatten the nested tree into nodes, links and clusters for the map.
     *
     * Every family (a root with derivatives) is a cluster; roots without
     * derivatives share one trailing "Independent" cluster and have no links.
     *
     * @param  list<array>  $roots  output of BuildFamilyTree
     * @param  callable(string): string  $urlFor  article URL for a wiki page slug
     * @return array{nodes: list<array>, links: list<array{source: string, target: string}>, clusters: list<array{id: int, name: string, standalone: bool}>}
     */
    public function execute(array $roots, callable $urlFor): array
    {
        $nodes = [];
        $links = [];
        $clusters = [];
        $lone = [];

        foreach ($roots as $root) {
            if ($root['children'] === []) {
                $lone[] = $root;

                continue;
            }

            $cluster = count($clusters);
            $clusters[] = ['id' => $cluster, 'name' => $root['name'], 'standalone' => false];
            $this->walk($root, null, $cluster, 0, $urlFor, $nodes, $links);
        }

        if ($lone !== []) {
            $cluster = count($clusters);
            $clusters[] = ['id' => $cluster, 'name' => self::STANDALONE, 'standalone' => true];

            foreach ($lone as $root) {
                $this->walk($root, null, $cluster, 0, $urlFor, $nodes, $links);
            }
        }

        return ['nodes' => $nodes, 'links' => $links, 'clusters' => $clusters];
    }

    /** @return int number of descendants of $node */
    private function walk(array $node, ?string $parent, int $cluster, int $depth, callable $urlFor, array &$nodes, array &$links): int
    {
        $index = count($nodes);
        $nodes[$index] = [
            'id' => $node['slug'],
            'name' => $node['name'],
            'accent' => $node['accent'],
            'summary' => $node['summary'],
            'url' => $node['page'] ? $urlFor($node['page']) : null,
            'parent' => $parent,
            'cluster' => $cluster,
            'depth' => $depth,
            'derivatives' => 0,
        ];

        $total = 0;

        foreach ($node['children'] as $child) {
            $links[] = ['source' => $node['slug'], 'target' => $child['slug']];
            $total += 1 + $this->walk($child, $node['slug'], $cluster, $depth + 1, $urlFor, $nodes, $links);
        }

        $nodes[$index]['derivatives'] = $total;

        return $total;
    }
}

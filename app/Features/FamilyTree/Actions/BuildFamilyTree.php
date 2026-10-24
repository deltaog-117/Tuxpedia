<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Actions;

use App\Features\DistroComparison\Models\Distribution;
use Illuminate\Support\Collection;

class BuildFamilyTree
{
    private const STANDALONE = 'independent';

    /**
     * Nest distributions under the distribution they are based on.
     *
     * Qualifiers such as "Ubuntu (LTS)" fold into their base name, and "Arch" matches
     * "Arch Linux". A parent with no row of its own ("Red Hat") becomes a bare node so
     * its derivatives still hang together. "Independent" and loops in the data make a root.
     *
     * @param  Collection<int, Distribution>  $distros
     * @return list<array{name: string, slug: string, accent: ?string, children: list<array>}>
     */
    public function execute(Collection $distros): array
    {
        $nodes = [];

        foreach ($distros as $distro) {
            $nodes[$this->key((string) $distro->name)] = [
                'name' => (string) $distro->name,
                'slug' => (string) $distro->slug,
                'accent' => $distro->accent?->toString(),
            ];
        }

        $parents = [];

        foreach ($distros as $distro) {
            $key = $this->key((string) $distro->name);
            $parent = $this->key((string) preg_replace('/\s*\(.*\)$/', '', (string) $distro->based_on));

            if ($parent === '' || $parent === self::STANDALONE || $parent === $key) {
                continue;
            }

            if (! isset($nodes[$parent])) {
                $nodes[$parent] = ['name' => trim((string) preg_replace('/\s*\(.*\)$/', '', (string) $distro->based_on)), 'slug' => $parent, 'accent' => null];
            }

            $parents[$key] = $parent;
        }

        $parents = $this->withoutLoops($parents);

        $children = [];
        $roots = [];

        foreach (array_keys($nodes) as $key) {
            isset($parents[$key]) ? $children[$parents[$key]][] = $key : $roots[] = $key;
        }

        return array_map(fn (string $key): array => $this->assemble($key, $nodes, $children), $roots);
    }

    /**
     * @param  array<string, string>  $parents
     * @return array<string, string>
     */
    private function withoutLoops(array $parents): array
    {
        foreach (array_keys($parents) as $start) {
            $seen = [$start => true];

            for ($key = $parents[$start] ?? null; $key !== null; $key = $parents[$key] ?? null) {
                if (isset($seen[$key])) {
                    unset($parents[$start]);
                    break;
                }

                $seen[$key] = true;
            }
        }

        return $parents;
    }

    /**
     * @param  array<string, array{name: string, slug: string, accent: ?string}>  $nodes
     * @param  array<string, list<string>>  $children
     */
    private function assemble(string $key, array $nodes, array $children): array
    {
        return $nodes[$key] + [
            'children' => array_map(fn (string $child): array => $this->assemble($child, $nodes, $children), $children[$key] ?? []),
        ];
    }

    /** "Arch" and "Arch Linux" must name the same distribution. */
    private function key(string $name): string
    {
        return (string) preg_replace('/\s+linux$/', '', mb_strtolower(trim($name)));
    }
}

<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\Actions;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\DistroComparison\ValueObjects\DistroProfile;
use Illuminate\Support\Collection;

final class BuildDistroProfile
{
    private const STANDALONE = 'independent';

    /**
     * Place a distribution among the others through the `based_on` column.
     *
     * Qualifiers such as "Ubuntu (LTS)" fold into their base name and "Arch" matches
     * "Arch Linux", as in the family tree. A parent with no row stays in the lineage as a
     * bare name, and a loop in the data ends the lineage instead of repeating it.
     *
     * @param  Collection<int, Distribution>  $all
     */
    public function execute(Distribution $distribution, Collection $all): DistroProfile
    {
        $byKey = $all->keyBy(fn (Distribution $other): string => $this->key((string) $other->name));
        $ownKey = $this->key((string) $distribution->name);
        $parentKey = $this->parentKey($distribution);

        $lineage = [];
        $seen = [$ownKey];
        $current = $distribution;

        while (($key = $this->parentKey($current)) !== '' && ! in_array($key, $seen, true)) {
            $seen[] = $key;
            $parent = $byKey->get($key);
            $lineage[] = ['name' => $parent?->name ?? $this->baseName($current), 'distribution' => $parent];

            if ($parent === null) {
                break;
            }

            $current = $parent;
        }

        $others = $all->filter(fn (Distribution $other): bool => $this->key((string) $other->name) !== $ownKey);

        return new DistroProfile(
            $distribution,
            array_reverse($lineage),
            $this->byName($others->filter(fn (Distribution $other): bool => $parentKey !== '' && $this->parentKey($other) === $parentKey)),
            $this->byName($others->filter(fn (Distribution $other): bool => $this->parentKey($other) === $ownKey)),
        );
    }

    /** Key of the parent, or '' for an independent distribution, a blank column or a distribution based on itself. */
    private function parentKey(Distribution $distribution): string
    {
        $key = $this->key($this->baseName($distribution));

        return $key === self::STANDALONE || $key === $this->key((string) $distribution->name) ? '' : $key;
    }

    /** `based_on` without its trailing qualifier: "Ubuntu (LTS)" gives "Ubuntu". */
    private function baseName(Distribution $distribution): string
    {
        return trim((string) preg_replace('/\s*\(.*\)$/', '', (string) $distribution->based_on));
    }

    /**
     * @param  Collection<int, Distribution>  $distributions
     * @return Collection<int, Distribution>
     */
    private function byName(Collection $distributions): Collection
    {
        return $distributions->sortBy(fn (Distribution $distribution): string => mb_strtolower((string) $distribution->name))->values();
    }

    /** "Arch" and "Arch Linux" must name the same distribution. */
    private function key(string $name): string
    {
        return (string) preg_replace('/\s+linux$/', '', mb_strtolower(trim($name)));
    }
}

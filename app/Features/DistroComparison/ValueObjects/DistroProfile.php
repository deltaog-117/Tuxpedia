<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\ValueObjects;

use App\Features\DistroComparison\Models\Distribution;
use Illuminate\Support\Collection;

/**
 * Where one distribution sits in the family: its ancestors, siblings and derivatives.
 *
 * A lineage entry carries the distribution when it has a row, or only a name when
 * the parent has none ("Red Hat"), so the view can link the first and print the second.
 */
final class DistroProfile
{
    /**
     * @param  list<array{name: string, distribution: ?Distribution}>  $lineage  root first, direct parent last
     * @param  Collection<int, Distribution>  $siblings  share the same parent, this distribution excluded
     * @param  Collection<int, Distribution>  $derivatives  based directly on this distribution
     */
    public function __construct(
        public readonly Distribution $distribution,
        public readonly array $lineage,
        public readonly Collection $siblings,
        public readonly Collection $derivatives,
    ) {}
}

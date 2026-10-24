<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Http\Livewire;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\FamilyTree\Actions\BuildFamilyTree;
use App\Features\Wiki\Models\Page;
use Livewire\Component;

class FamilyTree extends Component
{
    public function render(BuildFamilyTree $buildFamilyTree)
    {
        $distros = Distribution::all();
        $pageSlugs = Page::whereNotNull('distribution_id')
            ->pluck('slug', 'distribution_id')
            ->mapWithKeys(fn (string $slug, int $id): array => [$distros->firstWhere('id', $id)?->slug => $slug])
            ->all();

        $roots = collect($buildFamilyTree->execute($distros, $pageSlugs));

        // A root with no derivatives has no lineage to draw; those share one card.
        [$families, $independent] = $roots->partition(fn (array $root): bool => $root['children'] !== []);

        return view('features.family-tree.tree', [
            'families' => $families->values(),
            'independent' => $independent->values(),
        ]);
    }
}

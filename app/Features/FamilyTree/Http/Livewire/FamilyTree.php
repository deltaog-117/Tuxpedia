<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Http\Livewire;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\FamilyTree\Actions\BuildFamilyGraph;
use App\Features\FamilyTree\Actions\BuildFamilyTree;
use App\Features\Wiki\Models\Page;
use Livewire\Component;

class FamilyTree extends Component
{
    public function render(BuildFamilyTree $buildFamilyTree, BuildFamilyGraph $buildFamilyGraph)
    {
        $distros = Distribution::all();
        $pageSlugs = Page::whereNotNull('distribution_id')
            ->pluck('slug', 'distribution_id')
            ->mapWithKeys(fn (string $slug, int $id): array => [$distros->firstWhere('id', $id)?->slug => $slug])
            ->all();

        $roots = $buildFamilyTree->execute($distros, $pageSlugs);

        return view('features.family-tree.tree', [
            'graph' => $buildFamilyGraph->execute($roots, fn (string $slug): string => route('wiki.show', $slug)),
        ]);
    }
}

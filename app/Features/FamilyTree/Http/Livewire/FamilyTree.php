<?php

declare(strict_types=1);

namespace App\Features\FamilyTree\Http\Livewire;

use App\Features\DistroComparison\Models\Distribution;
use App\Features\FamilyTree\Actions\BuildFamilyTree;
use Livewire\Component;

class FamilyTree extends Component
{
    public function render(BuildFamilyTree $buildFamilyTree)
    {
        return view('features.family-tree.tree', [
            'tree' => $buildFamilyTree->execute(Distribution::all()),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Features\DistroComparison\Http\Controllers;

use App\Features\DistroComparison\Actions\BuildDistroProfile;
use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\ListRelatedPages;
use Illuminate\View\View;

class DistroController
{
    public function show(string $slug, BuildDistroProfile $buildProfile, ListRelatedPages $listRelated): View
    {
        $distribution = Distribution::where('slug', $slug)->firstOrFail();
        $article = $distribution->pages()->first();

        return view('features.distro-comparison.show', [
            'profile' => $buildProfile->execute($distribution, Distribution::all()),
            'article' => $article,
            'related' => $article === null ? collect() : $listRelated->execute($article),
        ]);
    }
}

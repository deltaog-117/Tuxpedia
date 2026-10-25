<?php

declare(strict_types=1);

namespace App\Features\Search\Http\Livewire;

use App\Features\Search\Actions\FilterPaletteCommands;
use App\Features\Search\Actions\FindPalettePages;
use Livewire\Component;

class CommandPalette extends Component
{
    public string $query = '';

    public function render(FilterPaletteCommands $commands, FindPalettePages $pages)
    {
        return view('features.search.palette', [
            'commands' => $commands->execute($this->query, auth()->check()),
            'pages' => $pages->execute($this->query),
        ]);
    }
}

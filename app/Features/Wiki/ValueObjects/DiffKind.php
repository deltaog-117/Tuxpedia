<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

enum DiffKind: string
{
    case Same = 'same';
    case Added = 'added';
    case Removed = 'removed';
}

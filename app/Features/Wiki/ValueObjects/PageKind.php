<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

/**
 * What a wiki page is: an article, a concept entry (kernel, init) or a page that
 * points an ambiguous name (Arch, Mint) at the pages it could mean.
 */
enum PageKind: string
{
    case Article = 'article';
    case Concept = 'concept';
    case Disambiguation = 'disambiguation';

    public function label(): string
    {
        return match ($this) {
            self::Article => 'Article',
            self::Concept => 'Concept',
            self::Disambiguation => 'Disambiguation',
        };
    }
}

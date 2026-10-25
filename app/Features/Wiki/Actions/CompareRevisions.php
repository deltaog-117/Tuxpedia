<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\ValueObjects\LineDiff;

final class CompareRevisions
{
    public function __construct(
        private ReadRevision $read,
    ) {}

    /**
     * @return list<\App\Features\Wiki\ValueObjects\DiffLine>
     */
    public function execute(Revision $from, Revision $to): array
    {
        return LineDiff::between(
            $this->read->execute($from)->toString(),
            $this->read->execute($to)->toString(),
        );
    }
}

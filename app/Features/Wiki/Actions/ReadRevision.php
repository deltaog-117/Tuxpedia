<?php

declare(strict_types=1);

namespace App\Features\Wiki\Actions;

use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\ValueObjects\Content;
use App\Shared\Services\GitService;

final class ReadRevision
{
    public function __construct(
        private GitService $git,
    ) {}

    public function execute(Revision $revision): Content
    {
        return Content::fromString($this->git->show($revision->sha, $revision->path));
    }
}

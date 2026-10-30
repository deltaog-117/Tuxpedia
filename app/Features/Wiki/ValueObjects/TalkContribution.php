<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use App\Features\Wiki\Models\Page;
use Carbon\CarbonInterface;

/**
 * One talk-page post by an account, either a topic it opened or a reply it wrote.
 */
final readonly class TalkContribution
{
    public function __construct(
        public Page $page,
        public int $topicId,
        public string $topicTitle,
        public bool $isReply,
        public string $excerpt,
        public CarbonInterface $at,
    ) {}
}

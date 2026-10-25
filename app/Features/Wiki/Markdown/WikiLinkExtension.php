<?php

declare(strict_types=1);

namespace App\Features\Wiki\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class WikiLinkExtension implements ExtensionInterface
{
    public function __construct(private WikiLinkParser $parser) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        // Above the bracket parser (priority 0), which would otherwise claim the "[".
        $environment->addInlineParser($this->parser, 100);
    }
}

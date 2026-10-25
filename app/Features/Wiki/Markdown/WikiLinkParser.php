<?php

declare(strict_types=1);

namespace App\Features\Wiki\Markdown;

use App\Features\Wiki\Exceptions\InvalidSlugException;
use App\Features\Wiki\ValueObjects\Slug;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

/**
 * Turns [[Target]] and [[Target|label]] into links to wiki pages.
 *
 * It is an inline parser, so code spans and code blocks are never touched.
 */
final class WikiLinkParser implements InlineParserInterface
{
    /** @var array<string, true> slugs found while parsing, keyed to keep them unique */
    private array $found = [];

    /**
     * @param  array<string, true>|null  $knownSlugs  slugs of existing pages; null renders every link as existing
     */
    public function __construct(private ?array $knownSlugs = null) {}

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('\[\[[^\[\]\n]+\]\]');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        [$target, $label] = array_pad(explode('|', substr($inlineContext->getFullMatch(), 2, -2), 2), 2, null);

        try {
            $slug = Slug::fromString(Str::slug(trim($target)));
        } catch (InvalidSlugException) {
            return false;
        }

        $this->found[$slug->toString()] = true;
        $exists = $this->knownSlugs === null || isset($this->knownSlugs[$slug->toString()]);

        $link = new Link(
            $exists ? route('wiki.show', $slug->toString()) : route('wiki.create', ['slug' => $slug->toString()]),
            trim((string) $label) !== '' ? trim($label) : trim($target),
        );
        $link->data->set('attributes/class', $exists ? 'wikilink' : 'wikilink wikilink--missing');
        if (! $exists) {
            $link->data->set('attributes/title', 'This page does not exist yet');
        }

        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $inlineContext->getContainer()->appendChild($link);

        return true;
    }

    /**
     * @return list<string>
     */
    public function foundSlugs(): array
    {
        return array_map('strval', array_keys($this->found));
    }
}

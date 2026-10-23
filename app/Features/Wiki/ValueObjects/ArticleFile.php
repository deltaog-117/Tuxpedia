<?php

declare(strict_types=1);

namespace App\Features\Wiki\ValueObjects;

use InvalidArgumentException;

/**
 * A wiki article stored as a Markdown file with a small front-matter header:
 *
 *     ---
 *     title: Debian
 *     author: Mara Voss
 *     distribution: debian
 *     ---
 *     Markdown body...
 *
 * The slug is the file name, so it can never disagree with the file on disk.
 */
final class ArticleFile
{
    private const REQUIRED_KEYS = ['title', 'author'];

    private const OPTIONAL_KEYS = ['distribution'];

    private function __construct(
        public readonly Slug $slug,
        public readonly Title $title,
        public readonly string $author,
        public readonly ?string $distributionSlug,
        public readonly Content $content,
    ) {
    }

    public static function parse(string $fileName, string $raw): self
    {
        $slug = Slug::fromString(pathinfo($fileName, PATHINFO_FILENAME));

        if (preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n(.*)\z/s', $raw, $parts) !== 1) {
            throw new InvalidArgumentException("{$fileName}: missing front matter block.");
        }

        $meta = self::parseMeta($fileName, $parts[1]);

        foreach (self::REQUIRED_KEYS as $key) {
            if (!isset($meta[$key])) {
                throw new InvalidArgumentException("{$fileName}: front matter is missing '{$key}'.");
            }
        }

        return new self(
            $slug,
            Title::fromString($meta['title']),
            $meta['author'],
            $meta['distribution'] ?? null,
            Content::fromString(trim($parts[2]) . "\n"),
        );
    }

    /**
     * @return array<string, string>
     */
    private static function parseMeta(string $fileName, string $block): array
    {
        $allowed = [...self::REQUIRED_KEYS, ...self::OPTIONAL_KEYS];
        $meta = [];

        foreach (preg_split('/\r?\n/', $block) as $line) {
            if (preg_match('/^([a-z_]+):\s*(.+)$/', $line, $pair) !== 1) {
                throw new InvalidArgumentException("{$fileName}: bad front matter line '{$line}'.");
            }
            if (!in_array($pair[1], $allowed, true)) {
                throw new InvalidArgumentException("{$fileName}: unknown front matter key '{$pair[1]}'.");
            }
            $meta[$pair[1]] = trim($pair[2]);
        }

        return $meta;
    }
}

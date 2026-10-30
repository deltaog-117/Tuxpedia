<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\DistroComparison\Database\Seeders\DistributionSeeder;
use App\Features\Wiki\Exceptions\InvalidSlugException;
use App\Features\Wiki\ValueObjects\ArticleFile;
use App\Features\Wiki\ValueObjects\PageKind;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArticleFileTest extends TestCase
{
    public function test_parses_front_matter_and_body(): void
    {
        $article = ArticleFile::parse(
            'debian.md',
            "---\ntitle: Debian\nauthor: Mara Voss\ndistribution: debian\n---\n### Overview\n\nText.\n\n"
        );

        $this->assertSame('debian', $article->slug->toString());
        $this->assertSame('Debian', $article->title->toString());
        $this->assertSame('Mara Voss', $article->author);
        $this->assertSame('debian', $article->distributionSlug);
        $this->assertSame("### Overview\n\nText.\n", $article->content->toString());
    }

    public function test_distribution_is_optional(): void
    {
        $article = ArticleFile::parse('notes.md', "---\ntitle: Notes\nauthor: A. Writer\n---\nBody\n");

        $this->assertNull($article->distributionSlug);
    }

    public function test_kind_defaults_to_article(): void
    {
        $article = ArticleFile::parse('notes.md', "---\ntitle: Notes\nauthor: A. Writer\n---\nBody\n");

        $this->assertSame(PageKind::Article, $article->kind);
    }

    public function test_reads_the_kind_from_front_matter(): void
    {
        $article = ArticleFile::parse('kernel.md', "---\ntitle: Kernel\nauthor: A. Writer\nkind: concept\n---\nBody\n");

        $this->assertSame(PageKind::Concept, $article->kind);
    }

    #[DataProvider('malformedProvider')]
    public function test_rejects_malformed_files(string $fileName, string $raw): void
    {
        $this->expectException(InvalidArgumentException::class);

        ArticleFile::parse($fileName, $raw);
    }

    public static function malformedProvider(): array
    {
        return [
            'no front matter' => ['a.md', "### Just a body\n"],
            'missing title' => ['a.md', "---\nauthor: X\n---\nBody\n"],
            'missing author' => ['a.md', "---\ntitle: X\n---\nBody\n"],
            'unknown key' => ['a.md', "---\ntitle: X\nauthor: Y\nlayout: wide\n---\nBody\n"],
            'unknown kind' => ['a.md', "---\ntitle: X\nauthor: Y\nkind: essay\n---\nBody\n"],
            'garbled line' => ['a.md', "---\ntitle X\nauthor: Y\n---\nBody\n"],
        ];
    }

    public function test_rejects_a_file_name_that_is_not_a_valid_slug(): void
    {
        $this->expectException(InvalidSlugException::class);

        ArticleFile::parse('Not A Slug.md', "---\ntitle: X\nauthor: Y\n---\nBody\n");
    }

    /**
     * Every shipped article must parse, name a seeded distribution, and stay inside what the
     * Markdown sanitiser keeps (raw HTML is stripped, so a stray tag would silently vanish).
     */
    public function test_all_shipped_articles_are_valid(): void
    {
        $seeded = array_column(DistributionSeeder::catalogue(), 'slug');
        $files = glob(__DIR__.'/../../../../database/content/wiki/*.md');

        $this->assertNotEmpty($files);
        $slugs = [];
        foreach ($files as $path) {
            $article = ArticleFile::parse(basename($path), (string) file_get_contents($path));

            $this->assertNotContains($article->slug->toString(), $slugs);
            $slugs[] = $article->slug->toString();
            $this->assertContains($article->distributionSlug, $seeded, basename($path));
            $this->assertDoesNotMatchRegularExpression('/<\/?[a-z][^>]*>/i', $article->content->toString(), basename($path));
            $this->assertStringNotContainsString('deltaog', strtolower($article->author.$article->content->toString()), basename($path));
            $this->assertGreaterThan(1500, strlen($article->content->toString()), basename($path));
        }
    }

    public function test_every_seeded_distribution_has_an_article(): void
    {
        $withArticle = array_map(
            fn (string $path): ?string => ArticleFile::parse(basename($path), (string) file_get_contents($path))->distributionSlug,
            glob(__DIR__.'/../../../../database/content/wiki/*.md')
        );

        foreach (DistributionSeeder::catalogue() as $distro) {
            $this->assertContains($distro['slug'], $withArticle, "No article for {$distro['slug']}");
        }
    }
}

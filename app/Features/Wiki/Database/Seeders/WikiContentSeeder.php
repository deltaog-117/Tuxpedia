<?php

declare(strict_types=1);

namespace App\Features\Wiki\Database\Seeders;

use App\Features\DistroComparison\Database\Seeders\DistributionSeeder;
use App\Features\DistroComparison\Models\Distribution;
use App\Features\Wiki\Actions\CreatePage;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\ValueObjects\ArticleFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Imports the Markdown articles in database/content/wiki.
 *
 * Safe to re-run: an existing page is never overwritten, because readers may
 * have edited it. It only gets its distribution link filled in when unset.
 */
class WikiContentSeeder extends Seeder
{
    public function run(CreatePage $createPage): void
    {
        // Articles reference distributions by slug, so they must exist first.
        $this->call(DistributionSeeder::class);

        $files = glob(database_path('content/wiki/*.md')) ?: [];
        sort($files);

        foreach ($files as $path) {
            $article = ArticleFile::parse(basename($path), (string) file_get_contents($path));
            $distributionId = $this->distributionIdFor($article);

            $page = Page::where('slug', $article->slug->toString())->first();

            if ($page === null) {
                $createPage->execute(
                    $article->slug,
                    $article->title,
                    $article->content,
                    $article->author,
                    $distributionId,
                );
                continue;
            }

            if ($page->distribution_id === null && $distributionId !== null) {
                $page->distribution_id = $distributionId;
                $page->save();
            }
        }

        Log::info('Wiki content seeded', ['files' => count($files)]);
    }

    private function distributionIdFor(ArticleFile $article): ?int
    {
        if ($article->distributionSlug === null) {
            return null;
        }

        $distribution = Distribution::where('slug', $article->distributionSlug)->first();
        if ($distribution === null) {
            throw new RuntimeException(
                "Article '{$article->slug}' names unknown distribution '{$article->distributionSlug}'."
            );
        }

        return $distribution->id;
    }
}

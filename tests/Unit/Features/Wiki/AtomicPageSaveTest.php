<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\Actions\CreatePage;
use App\Features\Wiki\Actions\RecordRevision;
use App\Features\Wiki\Actions\UpdatePage;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\Slug;
use App\Features\Wiki\ValueObjects\Title;
use App\Shared\Services\GitService;
use RuntimeException;
use Tests\TestCase;

class AtomicPageSaveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();

        (require app_path('Features/DistroComparison/Database/Migrations/2024_08_25_000000_create_distributions_table.php'))->up();

        // The FULLTEXT migration is MySQL-only, so build the schema without it.
        foreach (glob(app_path('Features/Wiki/Database/Migrations/*.php')) as $file) {
            if (! str_contains($file, 'fulltext')) {
                (require $file)->up();
            }
        }
    }

    private function git(bool $failCommit = false): GitService
    {
        return new class($failCommit) extends GitService
        {
            /** @var list<string> */
            public array $moves = [];

            public function __construct(private bool $failCommit) {}

            public function commitFile(string $filePath, string $content, string $commitMessage): ?string
            {
                if ($this->failCommit) {
                    throw new RuntimeException('Git command failed: disk full');
                }

                return sha1($filePath.$content);
            }

            public function moveFile(string $from, string $to): void
            {
                $this->moves[] = "{$from}>{$to}";
            }
        };
    }

    private function create(GitService $git, string $slug = 'alpha'): Page
    {
        return (new CreatePage($git, new RecordRevision))
            ->execute(Slug::fromString($slug), Title::fromString('Alpha'), Content::fromString('one'));
    }

    public function test_failed_commit_leaves_no_page_behind_on_create(): void
    {
        try {
            $this->create($this->git(failCommit: true));
            $this->fail('Expected the commit failure to propagate.');
        } catch (RuntimeException) {
            $this->assertSame(0, Page::count());
            $this->assertSame(0, Revision::count());
        }
    }

    public function test_successful_create_stores_the_page_and_its_revision(): void
    {
        $page = $this->create($this->git());

        $this->assertSame(1, Page::count());
        $this->assertSame(1, Revision::where('page_id', $page->id)->count());
    }

    public function test_failed_commit_keeps_the_old_text_on_update(): void
    {
        $page = $this->create($this->git());

        try {
            (new UpdatePage($this->git(failCommit: true), new RecordRevision))
                ->execute($page, Slug::fromString('alpha'), Title::fromString('Changed'), Content::fromString('two'));
            $this->fail('Expected the commit failure to propagate.');
        } catch (RuntimeException) {
            $stored = Page::findOrFail($page->id);
            $this->assertSame('Alpha', $stored->title->toString());
            $this->assertSame('one', $stored->content->toString());
            $this->assertSame('Alpha', $page->title->toString());
            $this->assertSame(1, Revision::count());
        }
    }

    public function test_failed_commit_after_a_rename_restores_slug_and_file(): void
    {
        $page = $this->create($this->git());
        $git = $this->git(failCommit: true);

        try {
            (new UpdatePage($git, new RecordRevision))
                ->execute($page, Slug::fromString('beta'), Title::fromString('Alpha'), Content::fromString('two'));
            $this->fail('Expected the commit failure to propagate.');
        } catch (RuntimeException) {
            $this->assertSame('alpha', Page::findOrFail($page->id)->slug->toString());
            $this->assertSame(['pages/alpha.md>pages/beta.md', 'pages/beta.md>pages/alpha.md'], $git->moves);
        }
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Shared\Services\GitService;
use PHPUnit\Framework\TestCase;

class GitServiceHistoryTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/tuxpedia-git-'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        exec('rm -rf '.escapeshellarg($this->dir));
    }

    // A user-level commit hook (commitlint) would reject commits in this throwaway repo.
    private function service(): GitService
    {
        $git = new GitService($this->dir);
        exec('git -C '.escapeshellarg($this->dir).' config core.hooksPath /dev/null');

        return $git;
    }

    public function test_commit_returns_sha_and_skips_unchanged_content(): void
    {
        $git = $this->service();

        $first = $git->commitFile('pages/a.md', 'one', 'docs(wiki): create page a');
        $same = $git->commitFile('pages/a.md', 'one', 'docs(wiki): update page a');
        $second = $git->commitFile('pages/a.md', 'two', 'docs(wiki): update page a');

        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', (string) $first);
        $this->assertNull($same);
        $this->assertNotSame($first, $second);
    }

    public function test_show_reads_content_at_a_commit(): void
    {
        $git = $this->service();
        $first = (string) $git->commitFile('pages/a.md', 'one', 'docs(wiki): create page a');
        $git->commitFile('pages/a.md', 'two', 'docs(wiki): update page a');

        $this->assertSame('one', $git->show($first, 'pages/a.md'));
    }

    public function test_log_follows_a_rename_and_reports_the_path_at_each_commit(): void
    {
        $git = $this->service();
        $git->commitFile('pages/old.md', "line one\nline two\nline three\n", 'docs(wiki): create page old');
        $git->moveFile('pages/old.md', 'pages/new.md');
        $git->commitFile('pages/new.md', "line one\nline two\nline three\nline four\n", 'docs(wiki): update page new');

        $entries = $git->log('pages/new.md');

        $this->assertSame(['pages/new.md', 'pages/new.md', 'pages/old.md'], array_column($entries, 'path'));
        $this->assertSame('docs(wiki): update page new', $entries[0]['subject']);
    }
}

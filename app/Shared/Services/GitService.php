<?php

declare(strict_types=1);

namespace App\Shared\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use RuntimeException;

class GitService
{
    protected string $repoPath;

    public function __construct(?string $repoPath = null)
    {
        $this->repoPath = $repoPath ?? storage_path('app/wiki-repo');
        $this->ensureRepoExists();
    }

    protected function ensureRepoExists(): void
    {
        if (!is_dir($this->repoPath)) {
            mkdir($this->repoPath, 0755, true);
            $this->initRepo();
        }
    }

    protected function initRepo(): void
    {
        $process = new Process(['git', 'init', $this->repoPath]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to initialize Git repository.');
        }
        $this->runGitCommand(['config', 'user.email', 'tuxpedia@example.com']);
        $this->runGitCommand(['config', 'user.name', 'Tuxpedia']);
    }

    protected function runGitCommand(array $args): string
    {
        $cmd = array_merge(['git', '-C', $this->repoPath], $args);
        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'Git command failed: ' . $process->getErrorOutput()
            );
        }
        return $process->getOutput();
    }

    /**
     * Commit the file and return the new commit's sha, or null when the
     * content is identical to what is already committed.
     */
    public function commitFile(string $filePath, string $content, string $commitMessage): ?string
    {
        $fullPath = $this->repoPath . '/' . $filePath;
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($fullPath, $content);

        $this->runGitCommand(['add', $filePath]);
        if (!$this->hasStagedChanges()) {
            return null;
        }
        $this->runGitCommand(['commit', '-m', $commitMessage]);

        return trim($this->runGitCommand(['rev-parse', 'HEAD']));
    }

    protected function hasStagedChanges(): bool
    {
        $process = new Process(['git', '-C', $this->repoPath, 'diff', '--cached', '--quiet']);
        $process->run();

        // Exit code 1 means differences; anything above is a real failure.
        if ($process->getExitCode() > 1) {
            throw new RuntimeException('Git command failed: ' . $process->getErrorOutput());
        }

        return $process->getExitCode() === 1;
    }

    /**
     * File content as it was at a commit.
     */
    public function show(string $sha, string $filePath): string
    {
        return $this->runGitCommand(['show', $sha . ':' . $filePath]);
    }

    /**
     * Commits that touched the file, newest first, following renames.
     *
     * @return list<array{sha: string, date: string, subject: string, path: string}>
     */
    public function log(string $filePath): array
    {
        $output = $this->runGitCommand([
            'log', '--follow', '--name-only', '--format=%x1e%H%x09%aI%x09%s', '--', $filePath,
        ]);

        $entries = [];
        foreach (array_filter(explode("\x1e", $output)) as $chunk) {
            [$header, $path] = array_pad(explode("\n", trim($chunk), 2), 2, '');
            [$sha, $date, $subject] = array_pad(explode("\t", $header, 3), 3, '');
            $entries[] = ['sha' => $sha, 'date' => $date, 'subject' => $subject, 'path' => trim($path)];
        }

        return $entries;
    }

    public function moveFile(string $from, string $to): void
    {
        $this->runGitCommand(['mv', $from, $to]);
        $this->runGitCommand(['commit', '-m', "docs(wiki): move {$from} to {$to}"]);
    }

    public function deleteFile(string $filePath, string $commitMessage): void
    {
        $this->runGitCommand(['rm', $filePath]);
        $this->runGitCommand(['commit', '-m', $commitMessage]);
    }

    public function getHistory(string $filePath): array
    {
        $output = $this->runGitCommand([
            'log', '--oneline', '--follow', '--', $filePath
        ]);
        return array_filter(explode("\n", trim($output)));
    }
}

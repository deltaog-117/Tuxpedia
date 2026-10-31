<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\Actions\BuildMarkdownExport;
use App\Features\Wiki\Models\Page;
use App\Features\Wiki\Models\Revision;
use App\Features\Wiki\ValueObjects\Content;
use Tests\TestCase;

class MarkdownExportTest extends TestCase
{
    private function page(): Page
    {
        return new Page(['slug' => 'arch-linux', 'title' => 'Arch Linux', 'content' => 'Body']);
    }

    public function test_current_export_adds_the_title_and_is_named_after_the_slug(): void
    {
        $export = (new BuildMarkdownExport)->execute($this->page(), Content::fromString("Rolling release.\n\n\n"));

        $this->assertSame('arch-linux.md', $export->filename);
        $this->assertSame("# Arch Linux\n\nRolling release.\n", $export->body);
    }

    public function test_content_that_already_has_a_title_is_not_given_a_second_one(): void
    {
        $export = (new BuildMarkdownExport)->execute($this->page(), Content::fromString("# Arch Linux\n\nText"));

        $this->assertSame("# Arch Linux\n\nText\n", $export->body);
    }

    public function test_revision_export_is_named_after_the_short_sha(): void
    {
        $revision = new Revision(['sha' => str_repeat('ab', 20)]);

        $export = (new BuildMarkdownExport)->execute($this->page(), Content::fromString('Text'), $revision);

        $this->assertSame('arch-linux-abababa.md', $export->filename);
    }

    public function test_empty_content_still_yields_a_titled_file(): void
    {
        $export = (new BuildMarkdownExport)->execute($this->page(), Content::fromString(''));

        $this->assertSame("# Arch Linux\n", $export->body);
    }
}

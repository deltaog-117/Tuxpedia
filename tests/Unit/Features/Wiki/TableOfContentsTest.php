<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\Content;
use App\Features\Wiki\ValueObjects\TableOfContents;
use Tests\TestCase;

class TableOfContentsTest extends TestCase
{
    private function toc(string $markdown): TableOfContents
    {
        return TableOfContents::fromHtml(Content::fromString($markdown)->toHtml());
    }

    public function test_it_lists_h2_and_h3_headings_in_order_and_skips_other_levels(): void
    {
        $entries = $this->toc("# Title\n\n## History\n\n### Early years\n\n#### Detail\n\n## Usage")->entries();

        $this->assertSame(['History', 'Early years', 'Usage'], array_map(fn ($e) => $e->text, $entries));
        $this->assertSame([2, 3, 2], array_map(fn ($e) => $e->level, $entries));
        $this->assertTrue($entries[1]->isSub());
    }

    public function test_entry_ids_match_the_anchors_in_the_article(): void
    {
        $html = Content::fromString("## Package manager\n\ntext\n\n## Init & boot")->toHtml();

        foreach (TableOfContents::fromHtml($html)->entries() as $entry) {
            $this->assertStringContainsString('id="'.$entry->id.'"', $html);
        }
    }

    public function test_repeated_headings_get_distinct_ids(): void
    {
        $ids = array_map(fn ($e) => $e->id, $this->toc("## Notes\n\n## Notes\n\n## Notes")->entries());

        $this->assertCount(3, array_unique($ids));
    }

    public function test_ids_are_unique_for_any_mix_of_headings(): void
    {
        mt_srand(117);
        $words = ['Setup', 'setup', 'Install', 'Notes', 'C++', 'Q&A', 'Über', '日本語', 'a b', 'a-b'];

        for ($round = 0; $round < 200; $round++) {
            $markdown = '';
            $count = mt_rand(2, 12);

            for ($i = 0; $i < $count; $i++) {
                $markdown .= str_repeat('#', mt_rand(2, 3)).' '.$words[mt_rand(0, count($words) - 1)]."\n\nbody\n\n";
            }

            $ids = array_map(fn ($e) => $e->id, $this->toc($markdown)->entries());

            $this->assertSame(count($ids), count(array_unique($ids)), $markdown);
        }
    }

    public function test_heading_markup_is_reduced_to_plain_text(): void
    {
        $entries = $this->toc("## The `apt` *tool*\n\n## Plain")->entries();

        $this->assertSame('The apt tool', $entries[0]->text);
    }

    public function test_a_page_needs_two_headings_to_show_a_contents_list(): void
    {
        $this->assertFalse($this->toc("## Only one\n\ntext")->isWorthShowing());
        $this->assertTrue($this->toc("## One\n\n## Two")->isWorthShowing());
        $this->assertFalse($this->toc('No headings at all')->isWorthShowing());
    }
}

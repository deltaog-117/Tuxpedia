<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\Content;
use Tests\TestCase;

class ContentTest extends TestCase
{
    public function test_it_renders_markdown_to_html(): void
    {
        $html = Content::fromString("# Title\n\nSome **bold** text.")->toHtml();

        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringContainsString('<strong>bold</strong>', $html);
    }

    public function test_it_strips_raw_html_and_scripts(): void
    {
        $html = Content::fromString("Hello <script>alert(1)</script> <img src=x onerror=alert(1)>")->toHtml();

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('onerror', $html);
    }

    public function test_it_drops_unsafe_link_schemes(): void
    {
        $html = Content::fromString('[click](javascript:alert(1))')->toHtml();

        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_it_keeps_safe_links(): void
    {
        $html = Content::fromString('[site](https://example.com)')->toHtml();

        $this->assertStringContainsString('href="https://example.com"', $html);
    }

    public function test_empty_content_renders_empty_html(): void
    {
        $this->assertSame('', trim(Content::fromString('')->toHtml()));
    }

    public function test_excerpt_is_the_first_paragraph_as_plain_text(): void
    {
        $excerpt = Content::fromString("### Overview\n\n**Arch** is a *rolling* [distro](https://x.test) & more.\n\nSecond paragraph.")->toExcerpt();

        $this->assertSame('Arch is a rolling distro & more.', $excerpt);
    }

    public function test_excerpt_is_cut_at_a_word_boundary_and_empty_without_a_paragraph(): void
    {
        $long = Content::fromString(str_repeat('word ', 100))->toExcerpt(30);

        $this->assertLessThanOrEqual(31, mb_strlen($long));
        $this->assertStringEndsWith('…', $long);
        $this->assertSame('', Content::fromString('### Heading only')->toExcerpt());
    }
}

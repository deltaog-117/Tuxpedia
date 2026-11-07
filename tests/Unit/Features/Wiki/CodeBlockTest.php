<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\ValueObjects\Content;
use Tests\TestCase;

/**
 * Colouring happens in the browser (public/js/highlight.js); the server's part is to
 * hand over the language name and an escaped body, which these tests pin down.
 */
class CodeBlockTest extends TestCase
{
    public function test_a_fence_with_a_language_keeps_it_as_a_class(): void
    {
        $html = Content::fromString("```bash\nsudo pacman -Syu\n```")->toHtml();

        $this->assertStringContainsString('<code class="language-bash">', $html);
    }

    public function test_a_bare_fence_has_no_language_class(): void
    {
        $html = Content::fromString("```\nplain output\n```")->toHtml();

        $this->assertStringNotContainsString('language-', $html);
    }

    public function test_code_is_escaped_so_the_highlighter_cannot_be_handed_markup(): void
    {
        $html = Content::fromString("```html\n<script>alert(1)</script>\n```")->toHtml();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_the_bundle_is_built_and_ignores_unnamed_languages(): void
    {
        $bundle = dirname(__DIR__, 4).'/public/js/highlight.js';

        $this->assertFileExists($bundle);
        $this->assertStringContainsString('language-', (string) file_get_contents($bundle));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Wiki;

use App\Features\Wiki\Actions\ParseCategoryNames;
use Illuminate\Support\Str;
use Tests\TestCase;

class ParseCategoryNamesTest extends TestCase
{
    public function test_names_are_split_trimmed_and_keyed_by_slug(): void
    {
        $result = (new ParseCategoryNames)->execute('  Init system ,Package   manager,, ');

        $this->assertSame(['init-system' => 'Init system', 'package-manager' => 'Package manager'], $result);
    }

    public function test_names_with_the_same_slug_count_once_and_the_first_spelling_wins(): void
    {
        $result = (new ParseCategoryNames)->execute('Init System, init-system, INIT  SYSTEM');

        $this->assertSame(['init-system' => 'Init System'], $result);
    }

    public function test_names_without_a_usable_slug_are_dropped(): void
    {
        $this->assertSame([], (new ParseCategoryNames)->execute('!!!, ,'));
        $this->assertSame([], (new ParseCategoryNames)->execute(''));
    }

    public function test_random_input_always_yields_unique_valid_slugs_within_the_limit(): void
    {
        $alphabet = str_split("abcXYZ019 -_,!é\t");
        $parse = new ParseCategoryNames;

        for ($i = 0; $i < 300; $i++) {
            $input = '';
            for ($j = random_int(0, 120); $j > 0; $j--) {
                $input .= $alphabet[array_rand($alphabet)];
            }

            $result = $parse->execute($input);

            $this->assertLessThanOrEqual(ParseCategoryNames::MAX_CATEGORIES, count($result));
            foreach ($result as $slug => $name) {
                $this->assertMatchesRegularExpression('/^[a-z0-9]+(-[a-z0-9]+)*$/', (string) $slug);
                $this->assertSame((string) $slug, Str::slug($name));
            }
        }
    }

    public function test_the_list_is_capped(): void
    {
        $input = implode(',', range(1, 50));

        $this->assertCount(ParseCategoryNames::MAX_CATEGORIES, (new ParseCategoryNames)->execute($input));
    }
}

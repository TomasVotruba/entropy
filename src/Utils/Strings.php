<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Entropy\Attribute\RelatedTest;
use Entropy\Tests\Utils\StringsTest;

/**
 * @api to be used outside
 * @see \Entropy\Tests\Utils\StringsTest
 */
#[RelatedTest(StringsTest::class)]
final class Strings
{
    public static function webalize(string $text): string
    {
        $text = (string) preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }
}

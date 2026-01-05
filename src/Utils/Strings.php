<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Utils\StringsTest;

#[RelatedTest(StringsTest::class)]
final class Strings
{
    public static function webalize(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
        $text = trim($text, '-');
        $text = mb_strtolower($text);

        return $text ?? '';
    }
}

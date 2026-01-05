<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Utils\RegexTest;

#[RelatedTest(RegexTest::class)]
final class Regex
{
    /**
     * @return string[]
     */
    public static function match(string $subject, string $pattern): array
    {
        $matches = [];
        preg_match($pattern, $subject, $matches);

        return $matches;
    }
}

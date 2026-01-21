<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Utils\RegexTest;

/**
 * @api to be used
 */
#[RelatedTest(RegexTest::class)]
final class Regex
{
    /**
     * @return array<string, mixed>
     */
    public static function match(string $subject, string $pattern): array
    {
        $matches = [];
        preg_match($pattern, $subject, $matches);

        return $matches;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function matchAll(string $subject, string $pattern): array
    {
        $matches = [];
        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

        return $matches;
    }

    public static function replace(string $subject, string $pattern, string|callable $replacement): string
    {
        if (is_callable($replacement)) {
            return (string) preg_replace_callback($pattern, $replacement, $subject);
        }

        return preg_replace($pattern, $replacement, $subject) ?? $subject;
    }
}

<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\Utils\FuzzyMatcherTest;

#[RelatedTest(FuzzyMatcherTest::class)]
final class FuzzyMatcher
{
    /**
     * @param string[] $candidates
     */
    public static function match(string $input, array $candidates): ?string
    {
        if ($input === '' || $candidates === []) {
            return null;
        }

        // 0. exact match (always safe)
        if (in_array($input, $candidates, true)) {
            return $input;
        }

        // 1. handle single-letter input STRICTLY
        if (strlen($input) === 1) {
            $prefixMatches = array_values(array_filter(
                $candidates,
                static fn (string $candidate): bool => str_starts_with($candidate, $input)
            ));

            return count($prefixMatches) === 1
                ? $prefixMatches[0]
                : null;
        }

        // 2. prefix match (multi-letter)
        $prefixMatches = array_values(array_filter(
            $candidates,
            static fn (string $candidate): bool => str_starts_with($candidate, $input)
        ));

        if (count($prefixMatches) === 1) {
            return $prefixMatches[0];
        }

        // 3. levenshtein typo match
        $distances = [];
        foreach ($candidates as $candidate) {
            $distances[$candidate] = levenshtein($input, $candidate);
        }

        asort($distances);

        $best = array_key_first($distances);
        $bestDistance = $distances[$best];

        // conservative threshold
        $maxAllowed = max(1, (int) floor(strlen($best) / 3));

        return $bestDistance <= $maxAllowed
            ? $best
            : null;
    }

    /**
     * @param string[] $candidates
     * @return string[]
     */
    public static function suggest(string $input, array $candidates): array
    {
        if ($input === '' || $candidates === []) {
            return [];
        }

        if (strlen($input) === 1) {
            return array_values(array_filter(
                $candidates,
                static fn (string $candidate): bool => str_starts_with($candidate, $input)
            ));
        }

        $scores = [];
        foreach ($candidates as $candidate) {
            $scores[$candidate] = levenshtein($input, $candidate);
        }

        asort($scores);

        return array_keys($scores);
    }
}

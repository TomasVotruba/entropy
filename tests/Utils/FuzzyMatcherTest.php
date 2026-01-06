<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\FuzzyMatcher;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FuzzyMatcherTest extends TestCase
{
    /**
     * @param string[] $candidates
     */
    #[DataProvider('provideMatchCases')]
    public function testMatch(string $input, array $candidates, ?string $expected): void
    {
        $this->assertSame($expected, FuzzyMatcher::match($input, $candidates));
    }

    /**
     * @return Iterator<array<int, (array<mixed>|string|null)>>
     */
    public static function provideMatchCases(): Iterator
    {
        yield ['', ['test'], null];
        yield ['test', [], null];

        yield ['test', ['test', 'status'], 'test'];

        yield ['e', ['test'], null];
        yield ['t', ['test'], 'test'];
        yield ['t', ['test', 'try'], null];

        yield ['sta', ['status', 'start'], null];
        yield ['stat', ['status', 'test'], 'status'];

        yield ['tset', ['test', 'status'], 'test'];
        yield ['xxxxxxxx', ['test', 'status'], null];
    }
}

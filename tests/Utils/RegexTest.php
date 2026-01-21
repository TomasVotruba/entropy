<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\Regex;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RegexTest extends TestCase
{
    public function testMatch(): void
    {
        $subject = 'Hello, my name is Joe';
        $pattern = '/name is (?<name>\w+)/';

        $matches = Regex::match($subject, $pattern);
        $this->assertSame('Joe', $matches['name']);
    }

    public function testMatchAll(): void
    {
        $subject = 'apple banana apple orange banana';
        $pattern = '/\b(?<fruit>\w+)\b/';

        $matches = Regex::matchAll($subject, $pattern);
        $this->assertCount(5, $matches);

        $this->assertSame('apple', $matches[0]['fruit']);
        $this->assertSame('banana', $matches[1]['fruit']);
        $this->assertSame('apple', $matches[2]['fruit']);
        $this->assertSame('orange', $matches[3]['fruit']);
        $this->assertSame('banana', $matches[4]['fruit']);
    }

    public function testReplace(): void
    {
        $subject = 'The quick brown fox';
        $pattern = '/brown/';
        $replacement = 'red';

        $result = Regex::replace($subject, $pattern, $replacement);
        $this->assertSame('The quick red fox', $result);
    }

    #[DataProvider('replacementDataProvider')]
    public function testReplaceWithClosure(string $subject, string $expectedResult): void
    {
        $pattern = '/brown (?<animal>\w+)/';

        $result = Regex::replace($subject, $pattern, function (array $matches): string {
            if (isset($matches['animal']) && $matches['animal'] === 'fox') {
                return 'white dog';
            }

            return 'black cat';
        });

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: string, 1: string}>
     */
    public static function replacementDataProvider(): iterable
    {
        yield ['The brown fox', 'The white dog'];
        yield ['The brown bird', 'The black cat'];
    }
}

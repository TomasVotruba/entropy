<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\Regex;
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

    public function testReplace(): void
    {
        $subject = 'The quick brown fox';
        $pattern = '/brown/';
        $replacement = 'red';

        $result = Regex::replace($subject, $pattern, $replacement);
        $this->assertSame('The quick red fox', $result);
    }
}

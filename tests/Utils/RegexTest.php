<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use PHPUnit\Framework\TestCase;

final class RegexTest extends TestCase
{
    public function testMatch(): void
    {
        $subject = 'Hello, my name is Joe';

        // named pattern
        $pattern = '/name is (?<name>\w+)/';

        $matches = \Entropy\Utils\Regex::match($subject, $pattern);

        $this->assertSame('Joe', $matches['name']);
    }
}

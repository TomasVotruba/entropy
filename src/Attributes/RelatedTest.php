<?php

declare(strict_types=1);

namespace Entropy\Attributes;

use Attribute;
use PHPUnit\Framework\TestCase;

#[Attribute(Attribute::TARGET_CLASS)]
final class RelatedTest
{
    /**
     * @param class-string<TestCase> $testCase
     */
    public function __construct(string $testCase)
    {
    }
}

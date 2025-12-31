<?php

namespace Entropy\Tests\Reflection\UseStatementsResolver\Fixture;

use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\AnotherNestedClass;
use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\EventMore\Nested;

final class SomeClass
{
    public function run(Nested $nested): AnotherNestedClass
    {
    }
}

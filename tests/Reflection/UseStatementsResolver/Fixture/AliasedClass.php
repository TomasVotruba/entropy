<?php

namespace Entropy\Tests\Reflection\UseStatementsResolver\Fixture;

use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\AnotherNestedClass as Aliased;
use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\EventMore\Nested;

final class AliasedClass
{
    public function run(Nested $nested, Aliased $aliased): void
    {
    }
}

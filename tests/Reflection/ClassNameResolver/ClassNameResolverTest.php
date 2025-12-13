<?php

namespace Entropy\Tests\Reflection\ClassNameResolver;

use Entropy\Reflection\ClassNameResolver;
use PHPUnit\Framework\TestCase;

final class ClassNameResolverTest extends TestCase
{
    public function test(): void
    {
        $className = ClassNameResolver::resolveFromFilePath(__DIR__ . '/Fixture/SomeClass.php');

        $this->assertSame('App\SomeNamespace\SomeClass', $className);
    }
}

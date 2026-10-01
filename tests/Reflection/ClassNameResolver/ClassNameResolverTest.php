<?php

declare(strict_types=1);

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

    public function testNothing(): void
    {
        $className = ClassNameResolver::resolveFromFilePath(__DIR__ . '/Fixture/bare-bin-file.php.inc');
        $this->assertSame(null, $className);
    }

    public function testResolveNames(): void
    {
        $classNames = ClassNameResolver::resolveNamesFromFilePath(__DIR__ . '/Fixture/SomeClass.php');
        $this->assertSame(['App\SomeNamespace\SomeClass'], $classNames);
    }

    public function testResolveNamesMultiple(): void
    {
        $classNames = ClassNameResolver::resolveNamesFromFilePath(__DIR__ . '/Fixture/TwoClasses.php.inc');
        $this->assertSame(['App\SomeNamespace\FirstClass', 'App\SomeNamespace\SecondClass'], $classNames);
    }
}

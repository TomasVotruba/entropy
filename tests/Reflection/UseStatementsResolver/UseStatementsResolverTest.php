<?php

declare(strict_types=1);

namespace Entropy\Tests\Reflection\UseStatementsResolver;

use Entropy\Reflection\UseStatementsResolver;
use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\AnotherNestedClass;
use Entropy\Tests\Reflection\UseStatementsResolver\Fixture\Nested\EventMore\Nested;
use PHPUnit\Framework\TestCase;

final class UseStatementsResolverTest extends TestCase
{
    public function test(): void
    {
        $useStatements = UseStatementsResolver::resolve(__DIR__ . '/Fixture/SomeClass.php');

        $this->assertSame([
            'AnotherNestedClass' => AnotherNestedClass::class,
            'Nested' => Nested::class,
        ], $useStatements);
    }

    public function testAliasedImport(): void
    {
        $useStatements = UseStatementsResolver::resolve(__DIR__ . '/Fixture/AliasedClass.php');

        $this->assertSame([
            'Aliased' => AnotherNestedClass::class,
            'Nested' => Nested::class,
        ], $useStatements);
    }

    public function testNoUseStatements(): void
    {
        $useStatements = UseStatementsResolver::resolve(__DIR__ . '/Fixture/NoUsesClass.php');

        $this->assertSame([], $useStatements);
    }

    public function testMissingFileReturnsEmpty(): void
    {
        $useStatements = @UseStatementsResolver::resolve(__DIR__ . '/Fixture/DoesNotExist.php');

        $this->assertSame([], $useStatements);
    }

    public function testTopLevelClassImport(): void
    {
        $useStatements = UseStatementsResolver::resolve(__DIR__ . '/Fixture/TopLevelUseClass.php');

        $this->assertSame([
            'Throwable' => 'Throwable',
        ], $useStatements);
        $this->assertArrayNotHasKey('', $useStatements);
    }
}

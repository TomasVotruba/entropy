<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\Strings;
use PHPUnit\Framework\TestCase;

final class StringsTest extends TestCase
{
    /**
     * @dataProvider webalizeDataProvider
     */
    public function testWebalize(string $input, string $expected): void
    {
        $result = Strings::webalize($input);
        $this->assertSame($expected, $result);
    }

    /**
     * @return iterable<array{0: string, 1: string}>
     */
    public static function webalizeDataProvider(): iterable
    {
        yield ['Hello World!', 'hello-world'];
        yield ['  PHP is great  ', 'php-is-great'];
        yield ['Café Münster', 'café-münster'];
        yield ['---Multiple---Dashes---', 'multiple-dashes'];
        yield ['No_Special*Chars@Here', 'no-special-chars-here'];
    }

    /**
     * @dataProvider afterDataProvider
     */
    public function testAfter(string $haystack, string $needle, int $nth, ?string $expected): void
    {
        $result = Strings::after($haystack, $needle, $nth);
        $this->assertSame($expected, $result);
    }

    /**
     * @return iterable<array{0: string, 1: string, 2: int, 3: string|null}>
     */
    public static function afterDataProvider(): iterable
    {
        yield ['App\Foo\Bar', '\\', -1, 'Bar'];
        yield ['App\Foo\Bar', '\\', 1, 'Foo\Bar'];
        yield ['App\Foo\Bar', '\\', 2, 'Bar'];
        yield ['NoBackslash', '\\', -1, null];
        yield ['NoBackslash', '\\', 1, null];
        yield ['a.b.c', '.', -1, 'c'];
    }
}

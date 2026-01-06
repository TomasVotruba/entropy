<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils;

use Entropy\Utils\Strings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StringsTest extends TestCase
{
    #[DataProvider('webalizeDataProvider')]
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
}

<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output;

use Entropy\Console\Output\OutputColorizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OutputColozierTest extends TestCase
{
    private OutputColorizer $outputColorizer;

    protected function setUp(): void
    {
        $this->outputColorizer = new OutputColorizer();
    }

    #[DataProvider('colorizeDataProvider')]
    public function testColorize(string $input, string $expected): void
    {
        $colorized = $this->outputColorizer->colorize($input);

        $this->assertSame($expected, $colorized);
    }

    public static function colorizeDataProvider(): iterable
    {
        // @note double quote must be used here, to preserve \e character as escape one
        yield ['some <fg=green>success</> next', "some \e[32msuccess\e[0m next"];
    }
}

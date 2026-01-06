<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Output;

use Entropy\Console\Output\OutputColorizer;
use Iterator;
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

    /**
     * @return Iterator<array<int, string>>
     */
    public static function colorizeDataProvider(): iterable
    {
        // @note double quote must be used here, to preserve \e character as escape one
        yield ['some <fg=green>success</> next', "some \e[32msuccess\e[0m next"];
        yield ['here <bg=yellow>orange</> is', "here \e[43;30m orange \e[0m is"];
    }
}

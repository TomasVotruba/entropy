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

    public function testBackgroundEqualLengthHasEqualVisibleWidth(): void
    {
        $text = '[OK] No errors found.';
        $emptyLine = str_repeat(' ', strlen($text));

        $backgroundText = $this->outputColorizer->background($text, 'green');
        $backgroundEmptyLine = $this->outputColorizer->background($emptyLine, 'green');

        $this->assertSame($this->visibleWidth($backgroundEmptyLine), $this->visibleWidth($backgroundText));
    }

    /**
     * @return Iterator<array<int, string>>
     */
    public static function colorizeDataProvider(): iterable
    {
        // @note double quote must be used here, to preserve \e character as escape one
        yield ['some <fg=green>success</> next', "some \e[32msuccess\e[0m next"];
        yield ['here <bg=yellow>orange</> is', "here \e[43;30m orange \e[0m is"];

        yield [
            ' * loading files from "<fg=green>%s</>" remote repository',
            " * loading files from \"\e[32m%s\e[0m\" remote repository",
        ];

        // buggy
        yield [
            ' * loading files from "<fg=green>https://github.com/rectorphp/rector-symfony.git</>" remote repository',
            " * loading files from \"\e[32mhttps://github.com/rectorphp/rector-symfony.git\e[0m\" remote repository",
        ];

        yield [
            <<<MULTILINE
some multline contents <fg=green>
all wrapped in green
</>
then finished
MULTILINE
            ,
            <<<MULTILINE
some multline contents \e[32m
all wrapped in green
\e[0m
then finished
MULTILINE
        ];
    }

    private function visibleWidth(string $text): int
    {
        // strip ANSI color escape sequences
        $stripped = (string) preg_replace('#\e\[[0-9;]*m#', '', $text);

        return strlen($stripped);
    }
}

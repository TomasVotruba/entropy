<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\Terminal;

use Entropy\Console\Terminal\Terminal;
use PHPUnit\Framework\TestCase;

final class TerminalTest extends TestCase
{
    private string|false $originalColumns;

    protected function setUp(): void
    {
        $this->originalColumns = getenv('COLUMNS');
    }

    protected function tearDown(): void
    {
        if ($this->originalColumns === false) {
            putenv('COLUMNS');
        } else {
            putenv('COLUMNS=' . $this->originalColumns);
        }
    }

    public function testGetWidthUsesColumnsEnv(): void
    {
        putenv('COLUMNS=80');

        $this->assertSame(80, Terminal::getWidth());
    }

    public function testGetWidthIsCappedAtMaxLineLength(): void
    {
        putenv('COLUMNS=999');

        $this->assertSame(120, Terminal::getWidth());
    }
}

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

    public function testGetWidthDoesNotWriteToStderrOnWindows(): void
    {
        if (\PHP_OS_FAMILY !== 'Windows') {
            self::markTestSkipped('This test requires Windows.');
        }

        $process = proc_open(
            [
                PHP_BINARY,
                '-r',
                "require 'vendor/autoload.php'; putenv('COLUMNS'); echo \\Entropy\\Console\\Terminal\\Terminal::getWidth();",
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            dirname(__DIR__, 3)
        );

        self::assertIsResource($process);

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        self::assertSame(0, $exitCode);
        self::assertSame('120', $stdout);
        self::assertSame('', $stderr);
    }
}

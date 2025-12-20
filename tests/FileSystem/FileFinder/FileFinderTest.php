<?php

declare(strict_types=1);

namespace Entropy\Tests\FileSystem\FileFinder;

use Entropy\FileSystem\FileFinder;
use PHPUnit\Framework\TestCase;

final class FileFinderTest extends TestCase
{
    public function test(): void
    {
        $phpFiles = FileFinder::findPhpFiles(__DIR__ . '/Fixture/some-dir');
        $this->assertCount(1, $phpFiles);
    }
}

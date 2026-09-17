<?php

declare(strict_types=1);

namespace Entropy\Tests\FileSystem\FileFinder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use PHPUnit\Framework\TestCase;

final class FileFinderTest extends TestCase
{
    public function test(): void
    {
        $phpFiles = FileFinder::findPhpFiles(__DIR__ . '/Fixture/some-dir');
        $this->assertCount(2, $phpFiles);
    }

    public function testFind(): void
    {
        $fileInfos = FileFinder::find([__DIR__ . '/Fixture/some-dir']);
        $this->assertCount(2, $fileInfos);
        $this->assertContainsOnlyInstancesOf(FileInfo::class, $fileInfos);
    }

    public function testFindWithFilter(): void
    {
        $fileInfos = FileFinder::find(
            [__DIR__ . '/Fixture/some-dir'],
            static fn (FileInfo $fileInfo): bool => ! str_contains($fileInfo->getRelativePathname(), 'vendor/')
        );

        $this->assertCount(1, $fileInfos);

        $fileInfo = $fileInfos[0];
        $this->assertSame('src/SomeFile.php', $fileInfo->getRelativePathname());
        $this->assertSame('src', $fileInfo->getRelativePath());
    }
}

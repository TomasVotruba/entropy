<?php

declare(strict_types=1);

namespace Entropy\FileSystem;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\FileSystem\FileFinder\FileFinderTest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

#[RelatedTest(FileFinderTest::class)]
final class FileFinder
{
    /**
     * @api used in tests
     * @return string[]
     */
    public static function findPhpFiles(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            if (self::isNonService($fileInfo)) {
                continue;
            }

            $files[] = $fileInfo->getPathname();
        }

        return $files;
    }

    private static function isNonService(SplFileInfo $fileInfo): bool
    {
        if (str_contains($fileInfo->getPathname(), '/ValueObject/')) {
            return true;
        }

        return str_contains($fileInfo->getPathname(), '/Enum/');
    }
}

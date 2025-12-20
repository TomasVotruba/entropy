<?php

declare(strict_types=1);

namespace Entropy\FileSystem;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\FileSystem\FileFinder\FileFinderTest;

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
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            /** @var \SplFileInfo $fileInfo */
            if (str_contains($fileInfo->getPathname(), 'vendor')) {
                continue;
            }

            if (str_contains($fileInfo->getPathname(), '/ValueObject/')) {
                continue;
            }

            $files[] = $fileInfo->getPathname();
        }

        return $files;
    }
}

<?php

declare(strict_types=1);

namespace Entropy\FileSystem;

use Entropy\Attributes\RelatedTest;
use Entropy\Tests\FileSystem\FileFinder\FileFinderTest;

#[RelatedTest(FileFinderTest::class)]
final class FileFinder
{
    /**
     * @var string[]
     */
    private const array SOURCE_DIRECTORIES = ['app', 'src'];

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

    /**
     * @return string[]
     */
    private static function findSourceDirectories(string $directory): array
    {
        $sourceDirectories = [];

        foreach (new \DirectoryIterator($directory) as $fileInfo) {
            /** @var \DirectoryIterator $fileInfo */
            if ($fileInfo->isDot()) {
                continue;
            }

            if (! $fileInfo->isDir()) {
                continue;
            }

            if (! in_array($fileInfo->getBasename(), self::SOURCE_DIRECTORIES, true)) {
                continue;
            }

            $sourceDirectories[] = $fileInfo->getPathname();
        }

        return $sourceDirectories;
    }
}

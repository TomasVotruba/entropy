<?php

declare(strict_types=1);

namespace Entropy\FileSystem;

use Entropy\Attributes\RelatedTest;

#[RelatedTest(\Entropy\Tests\FileSystem\FileFinder\FileFinderTest::class)]
final class FileFinder
{
    /**
     * @var string[]
     */
    private const SOURCE_DIRECTORIES = ['app', 'src'];

    public static function findSourcePhpFiles(string $directory): array
    {
        $sourceDirectories = self::findSourceDirectories($directory);

        $phpFiles = [];
        foreach ($sourceDirectories as $sourceDirectory) {
            $phpFiles = array_merge($phpFiles, self::findPhpFiles($sourceDirectory));
        }

        return $phpFiles;
    }

    /**
     * @return string[]
     */
    public static function findSourceDirectories(string $directory): array
    {
        $sourceDirectories = [];

        foreach (new \DirectoryIterator($directory) as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
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

    /**
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

            $files[] = $fileInfo->getPathname();
        }

        return $files;
    }
}

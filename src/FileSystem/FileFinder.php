<?php

declare(strict_types=1);

namespace Entropy\FileSystem;

use Entropy\Attributes\RelatedTest;

#[RelatedTest(\Entropy\Tests\FileSystem\FileFinder\FileFinderTest::class)]
final class FileFinder
{
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

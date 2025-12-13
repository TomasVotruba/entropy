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

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}

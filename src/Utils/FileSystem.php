<?php

declare(strict_types=1);

namespace Entropy\Utils;

use Webmozart\Assert\Assert;

/**
 * @api public api to use
 */
final class FileSystem
{
    public static function read(string $filePath): string
    {
        Assert::fileExists($filePath);

        $fileContents = file_get_contents($filePath);
        Assert::notFalse($fileContents, sprintf('Failed to read the "%s" file', $filePath));

        return $fileContents;
    }

    /**
     * @return array<string, mixed>
     */
    public static function loadFileToJson(string $filePath): array
    {
        Assert::fileExists($filePath);

        $fileContents = self::read($filePath);

        return json_decode($fileContents, true, 512, JSON_THROW_ON_ERROR);
    }
}

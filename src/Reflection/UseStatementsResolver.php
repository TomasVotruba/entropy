<?php

declare(strict_types=1);

namespace Entropy\Reflection;

final class UseStatementsResolver
{
    /**
     * @return array<string, string> Mapping of short class names to fully qualified class names
     */
    public static function resolve(string $filePath): array
    {
        $useStatements = [];
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            return $useStatements;
        }

        $pattern = '/use\s+([a-zA-Z0-9_\\\\]+)(\s+as\s+([a-zA-Z0-9_]+))?;/';
        preg_match_all($pattern, $fileContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fullClassName = $match[1];
            $shortClassName = isset($match[3]) ? $match[3] : substr(strrchr($fullClassName, '\\'), 1);
            $useStatements[$shortClassName] = $fullClassName;
        }

        return $useStatements;

    }
}

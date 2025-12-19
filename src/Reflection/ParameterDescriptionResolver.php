<?php

declare(strict_types=1);

namespace Entropy\Reflection;

use Entropy\Attributes\RelatedTest;
use ReflectionMethod;

#[RelatedTest(\Entropy\Tests\Reflection\ParameterDescriptionResolver\ParameterDescriptionResolverTest::class)]
final class ParameterDescriptionResolver
{
    /**
     * @return array<string, string> map: paramName => description
     */
    public static function resolve(ReflectionMethod $reflectionMethod): array
    {
        $doc = $reflectionMethod->getDocComment();
        if ($doc === false || $doc === '') {
            return [];
        }

        // Match lines like:
        // @param string $name Description...
        $pattern = '/@param\s+[^\s]+\s+\$([A-Za-z_][A-Za-z0-9_]*)\s*(.*)$/m';

        if (preg_match_all($pattern, $doc, $matches, PREG_SET_ORDER) !== 1 && $matches === []) {
            return [];
        }

        $descriptions = [];

        foreach ($matches as $match) {
            $paramName = $match[1];
            $desc = trim($match[2]);

            if ($desc !== '') {
                $descriptions[$paramName] = $desc;
            }
        }

        return $descriptions;
    }
}

<?php

declare(strict_types=1);

namespace Entropy\Reflection;

final class ClassNameResolver
{
    public static function resolveFromFilePath(string $filePath): ?string
    {
        $code = file_get_contents($filePath);
        $tokens = token_get_all($code);

        $namespace = '';
        $class = null;

        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            // namespace App\SomeNamespace;
            if ($token[0] === T_NAMESPACE) {
                $namespace = '';

                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j] === ';') {
                        break;
                    }

                    if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, T_NAME_QUALIFIED], true)) {
                        $namespace .= $tokens[$j][1];
                    }
                }
            }

            // class SomeClass
            if ($token[0] === T_CLASS) {
                // skip anonymous classes
                if ($tokens[$i - 1][0] === T_NEW) {
                    continue;
                }

                // next T_STRING is the class name
                for ($j = $i + 1; $j < $count; $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        break;
                    }
                }
            }
        }

        if ($class === null) {
            return null;
        }

        return $namespace !== ''
            ? $namespace . '\\' . $class
            : $class;
    }
}

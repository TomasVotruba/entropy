<?php

declare(strict_types=1);

namespace Entropy\Reflection;

final class ClassNameResolver
{
    /**
     * Created by GPT
     *
     * Return all class-like FQNs (class, interface, trait, enum) declared in a PHP file,
     * without autoloading or PHP-Parser. Uses token_get_all().
     *
     * @return string[] list of FQNs in declaration order (may be empty)
     */
    public static function resolveFromFilePath(string $filePath): ?string
    {
        $code = @file_get_contents($filePath);
        if ($code === false) {
            return null;
        }

        $tokens = token_get_all($code);

        $namespace = '';
        $fqns = [];

        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $t = $tokens[$i];
            if (! is_array($t)) {
                continue;
            }

            // Handle "namespace Foo\Bar;" and "namespace Foo\Bar {"
            if ($t[0] === T_NAMESPACE) {
                $namespace = '';
                for ($j = $i + 1; $j < $count; $j++) {
                    $tj = $tokens[$j];

                    if ($tj === ';' || $tj === '{') {
                        break;
                    }

                    if (! is_array($tj)) {
                        continue;
                    }

                    // PHP 8+: T_NAME_QUALIFIED can appear; PHP 7: it's T_STRING + T_NS_SEPARATOR
                    if ($tj[0] === T_STRING || $tj[0] === T_NS_SEPARATOR || (defined(
                        'T_NAME_QUALIFIED'
                    ) && $tj[0] === T_NAME_QUALIFIED)) {
                        $namespace .= $tj[1];
                    }
                }

                $namespace = trim($namespace, "\\ \t\n\r\0\x0B");
                continue;
            }

            // Detect class-likes (class/interface/trait/enum)
            $isEnum = defined('T_ENUM') && $t[0] === T_ENUM;
            if ($t[0] !== T_CLASS && $t[0] !== T_INTERFACE && $t[0] !== T_TRAIT && ! $isEnum) {
                continue;
            }

            // Skip anonymous classes: "new class (...)"
            $prev = self::previousNonWhitespaceToken($tokens, $i);
            if (is_array($prev) && $prev[0] === T_NEW) {
                continue;
            }

            // Next T_STRING is the name (skip whitespace, attributes/comments, etc.)
            $name = null;
            for ($j = $i + 1; $j < $count; $j++) {
                $tj = $tokens[$j];

                if (! is_array($tj)) {
                    continue;
                }

                if ($tj[0] === T_STRING) {
                    $name = $tj[1];
                    break;
                }

                // If we hit "{" or "(" before name, something is off (e.g. anonymous or invalid)
                if ($tj[0] === ord('{') || $tj[0] === ord('(')) {
                    break;
                }
            }

            if ($name === null) {
                continue;
            }

            $fqns[] = $namespace !== '' ? $namespace . '\\' . $name : $name;
        }

        // De-duplicate while preserving order (in case of weird tokenization)
        $fqn = array_unique($fqns);
        if (count($fqn) === 1) {
            return $fqn[0];
        }

        return null;
    }

    /**
     * @param array<int, mixed> $tokens
     * @return array{0:int,1:string,2:int}|string|null
     */
    private static function previousNonWhitespaceToken(array $tokens, int $index)
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            $t = $tokens[$i];

            if (is_array($t) && ($t[0] === T_WHITESPACE || $t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT)) {
                continue;
            }

            return $t;
        }

        return null;
    }
}

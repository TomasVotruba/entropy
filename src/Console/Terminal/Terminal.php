<?php

declare(strict_types=1);

namespace Entropy\Console\Terminal;

final class Terminal
{
    public static function padVisibleRight(string $text, int $width, string $padChar = ' '): string
    {
        $len = self::visibleLength($text);

        if ($len >= $width) {
            return $text;
        }

        return $text . str_repeat($padChar, $width - $len);
    }

    private static function visibleLength(string $text): int
    {
        // remove console meta tags like <fg=green> ... </> and <bg=red> ... </>
        $stripped = preg_replace('#</?>|<(?:fg|bg)=(?:green|yellow|red|cyan|orange)>#', '', $text);
        $stripped ??= $text;

        // if you might output UTF-8 (✔ etc.), prefer mb_strlen
        return function_exists('mb_strlen') ? mb_strlen($stripped) : strlen($stripped);
    }
}

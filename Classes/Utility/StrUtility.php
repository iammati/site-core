<?php

declare(strict_types=1);

namespace Site\Core\Utility;

/**
 * A collection of simple string utilities
 */
class StrUtility
{
    /**
     * Checks if the given string ($haystack) starts with a number.
     */
    public static function startsWithNumber(string $haystack): bool
    {
        return preg_match('/^\d/', $haystack) === 1;
    }

    /**
     * Converts the given $haystack-string into a server-side-friendly string.
     * E.g. '/references/My%20References%20By%20tESttt' would turn into 'references-my-references-by-testtt'.
     */
    public static function convertUri(string $haystack, string $separator = '-'): string
    {
        $accentsRegex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';

        $specialCases = [
            '&' => 'and',
            "'" => '',
        ];

        $haystack = mb_strtolower(trim($haystack), 'UTF-8');
        $haystack = str_replace(array_keys($specialCases), array_values($specialCases), $haystack);

        $haystack = preg_replace($accentsRegex, '$1', htmlentities($haystack, ENT_QUOTES, 'UTF-8'));
        $haystack = preg_replace('/[^a-z0-9]/u', $separator, $haystack);

        // If requested URI starts with a '/' (slash),
        // it'll be converted into a '-' (dash).
        // In that case it'll be checked if it starts then with a dash
        // and removes that.
        if (str_starts_with($haystack, '-')) {
            $haystack = mb_substr($haystack, 1);
        }

        // Same as above, just with an endsWith-condition.
        if (str_ends_with($haystack, '-')) {
            $haystack = mb_substr($haystack, 0, mb_strlen($haystack) - 1);
        }

        return $haystack;
    }

    /**
     * Converts a string e.g. 'ThisIsMyString' to 'this_is_my_string'.
     * Basically UpperCamelCase to snake_case.
     */
    public static function toSnakeCase(string $str, string $glue = '_'): string
    {
        $snakeCaseStr = preg_replace_callback(
            '/[A-Z]/',
            function ($matches) use ($glue) {
                return $glue.strtolower($matches[0]);
            },
            $str
        );

        if (self::startsWith($snakeCaseStr, '_')) {
            return substr($snakeCaseStr, 1, strlen($snakeCaseStr));
        }

        return $snakeCaseStr;
    }
}

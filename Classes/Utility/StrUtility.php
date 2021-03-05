<?php

declare(strict_types=1);

namespace Site\Core\Utility;

/**
 * String Utility Class.
 *
 * Extend it with any method you would like to have for strings which
 * would be a common case.
 */
class StrUtility
{
    /**
     * Checks if the given string ($haystack) starts with the other given string ($needle).
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle)
    {
        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * Checks if the given string ($haystack) ends with the other given string ($needle).
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle)
    {
        $length = strlen($needle);

        if (!$length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    /**
     * Checks if the given string ($haystack) contains the other given string ($needle).
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Checks if the given string ($haystack) starts with a number.
     *
     * @param string $haystack
     *
     * @return bool
     */
    public static function startsWithNumber($haystack)
    {
        return preg_match('/^\d/', $haystack) === 1;
    }

    /**
     * Converts the given $haystack-string into a server-side-friendly string.
     * E.g. '/references/My%20References%20By%20tESttt' would turn into 'references-my-references-by-testtt'.
     *
     * @param string $separator
     *
     * @return string
     */
    public static function convertUri(string $haystack, $separator = '-')
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

        if ($separator != '') {
            $haystack = preg_replace('/[$separator]+/u', $separator, $haystack);
        }

        // If requested URI starts with a '/' (slash),
        // it'll be converted into a '-' (dash).
        // In that case it'll be checked if it starts then with a dash
        // and removes that.
        if (self::startsWith($haystack, '-')) {
            $haystack = mb_substr($haystack, 1);
        }

        // Same as above, just with an endsWith-condition.
        if (self::endsWith($haystack, '-')) {
            $haystack = mb_substr($haystack, 0, mb_strlen($haystack) - 1);
        }

        return $haystack;
    }

    /**
     * Converts a string e.g. 'ThisIsMyString' to 'this_is_my_string' - CamelCase to snake_case.
     *
     * @param string $str
     * @param string $glue
     *
     * @return void
     */
    public static function toSnakeCase($str, $glue = '_') {
        $snakeCaseStr =  preg_replace_callback(
            '/[A-Z]/',
            fn($matches) => $glue . strtolower($matches[0]),
            $str
        );

        if (self::startsWith($snakeCaseStr, '_')) {
            return substr($snakeCaseStr, 1, strlen($snakeCaseStr));
        }

        return $snakeCaseStr;
    }
}

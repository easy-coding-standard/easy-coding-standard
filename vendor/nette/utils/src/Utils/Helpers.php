<?php

namespace ECSPrefix20210507\Nette\Utils;

class Helpers
{
    /**
     * Executes a callback and returns the captured output as a string.
     * @return string
     */
    public static function capture(callable $func)
    {
        \ob_start(function () {
        });
        try {
            $func();
            return \ob_get_clean();
        } catch (\Throwable $e) {
            \ob_end_clean();
            throw $e;
        }
    }
    /**
     * Returns the last occurred PHP error or an empty string if no error occurred. Unlike error_get_last(),
     * it is nit affected by the PHP directive html_errors and always returns text, not HTML.
     * @return string
     */
    public static function getLastError()
    {
        $message = isset(\error_get_last()['message']) ? \error_get_last()['message'] : '';
        $message = \ini_get('html_errors') ? \ECSPrefix20210507\Nette\Utils\Html::htmlToText($message) : $message;
        $message = \preg_replace('#^\\w+\\(.*?\\): #', '', $message);
        return $message;
    }
    /**
     * Converts false to null, does not change other values.
     * @param  mixed  $value
     * @return mixed
     */
    public static function falseToNull($value)
    {
        return $value === \false ? null : $value;
    }
    /**
     * Looks for a string from possibilities that is most similar to value, but not the same (for 8-bit encoding).
     * @param  string[]  $possibilities
     * @return string|null
     * @param string $value
     */
    public static function getSuggestion(array $possibilities, $value)
    {
        $best = null;
        $min = (\strlen($value) / 4 + 1) * 10 + 0.1;
        foreach (\array_unique($possibilities) as $item) {
            if ($item !== $value && ($len = \levenshtein($item, $value, 10, 11, 10)) < $min) {
                $min = $len;
                $best = $item;
            }
        }
        return $best;
    }
}

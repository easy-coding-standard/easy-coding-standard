<?php

declare(strict_types=1);

if (! function_exists('dd')) {
    function dd(mixed $var): void
    {
        dump($var);
        die;
    }
}

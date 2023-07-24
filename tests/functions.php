<?php

declare(strict_types=1);

// create dd() function if it does not exist
if (! function_exists('dd')) {
    function dd(mixed $args): void
    {
        foreach ($args as $arg) {
            dump($arg);
        }

        die;
    }
}

<?php

// create dd() function if it does not exist
if (! function_exists('dd')) {
    function dd(...$args): void
    {
        foreach ($args as $arg) {
            dump($arg);
        }
        die;
    }
}


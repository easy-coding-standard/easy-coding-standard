<?php

declare(strict_types=1);

function dd(mixed $var): void
{
    dump($var);
    die;
}

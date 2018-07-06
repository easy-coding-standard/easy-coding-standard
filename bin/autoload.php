<?php declare(strict_types=1);

$possibleAutoloadPaths = [
    // composer create-project
    __DIR__ . '/../../..',
    // composer require
    __DIR__ . '/../vendor',
    // mono-repository
    __DIR__ . '/../../../vendor',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (is_file($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        require_once $possibleAutoloadPath . '/squizlabs/php_codesniffer/autoload.php';

        break;
    }
}

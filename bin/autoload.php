<?php declare(strict_types=1);

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor',
    // dependency
    __DIR__ . '/../../..',
    // monorepo
    __DIR__ . '/../../../vendor',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (is_file($possibleAutoloadPath . '/autoload.php')) {
        require_once $possibleAutoloadPath . '/autoload.php';
        require_once $possibleAutoloadPath . '/squizlabs/php_codesniffer/autoload.php';

        break;
    }
}

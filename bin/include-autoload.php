<?php declare(strict_types=1);

gc_disable(); // performance boost

$possibleAutoloadFileLocations = [
    getcwd() . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../../vendor/autoload.php'
];

$isAutoloadLoaded = false;
foreach ($possibleAutoloadFileLocations as $autoloadFileLocation) {
    if (file_exists($autoloadFileLocation)) {
        require_once $autoloadFileLocation;
        $isAutoloadLoaded = true;
    }
}

if ($isAutoloadLoaded === false) {
    echo 'You must set up the project dependencies, run the following commands:' . PHP_EOL .
        'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
        'php composer.phar install' . PHP_EOL;
    exit(1);
}

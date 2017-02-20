<?php declare(strict_types=1);

if (! (new AutoloadIncluder)->includeAutoload()) {
    echo 'You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL;
    exit(1);
}

final class AutoloadIncluder
{
    /**
     * @var string[]
     */
    private $possibleAutoloadFileLocations = [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
        __DIR__ . '/../../../vendor/autoload.php'
    ];

    public function includeAutoload() : bool
    {
        foreach ($this->possibleAutoloadFileLocations as $autoloadFileLocation) {
            if ($this->includeFileIfExists($autoloadFileLocation)) {
                return true;
            }
        }

        return false;
    }

    private function includeFileIfExists(string $file) : bool
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }

        return false;
    }
}

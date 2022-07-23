<?php

declare(strict_types=1);

// inspired by https://github.com/phpstan/phpstan/blob/master/bootstrap.php

use Composer\Autoload\ClassLoader;

spl_autoload_register(static function (string $class): void {
    static $composerAutoloader;
    // already loaded in bin/ecs.php
    if (defined('__ECS_RUNNING__')) {
        return;
    }

    // load prefixed or native class, e.g. for running tests
    if (strncmp($class, 'ECSPrefix', strlen('ECSPrefix')) === 0 || strncmp($class, 'Symplify\\', strlen('Symplify\\')) === 0) {
        if ($composerAutoloader === null) {
            // prefixed version autoload
            $composerAutoloader = require __DIR__ . '/vendor/autoload.php';
        }

        // avoid autoloading collision
        if ($composerAutoloader instanceof ClassLoader) {
            $composerAutoloader->loadClass($class);
        }
    }
});

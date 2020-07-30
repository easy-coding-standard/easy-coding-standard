<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

$excludedClasses = [
    // part of public API in config
    ContainerConfigurator::class
];

return [
    'prefix' => null,
    'finders' => [],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if ($filePath !== 'bin/ecs') {
                return $content;
            }
            return str_replace("__DIR__ . '/..", "'phar://ecs.phar", $content);
        },

        // unprefix excluded classes
        // fixes https://github.com/humbug/box/issues/470
        function (string $filePath, string $prefix, string $content) use ($excludedClasses): string {
            foreach ($excludedClasses as $excludedClass) {
                $prefixedClassPattern = '#' . $prefix . '\\\\' . preg_quote($excludedClass, '#') . '#';
                $content = Strings::replace($content, $prefixedClassPattern, $excludedClass);
            }

            return $content;
        },
    ],
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
        'PhpCsFixer\*',
        'PHP_CodeSniffer\*',
        'SlevomatCodingStandard\*',
        ContainerConfigurator::class,
    ],
];

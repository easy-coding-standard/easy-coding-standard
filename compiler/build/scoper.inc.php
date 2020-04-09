<?php

declare(strict_types=1);

return [
    'prefix' => null,
    'finders' => [],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            if ($filePath !== 'bin/ecs') {
                return $content;
            }
            return str_replace('__DIR__ . \'/..', '\'phar://ecs.phar', $content);
        },
    ],
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*', 'PhpCsFixer\*', 'PHP_CodeSniffer\*', 'SlevomatCodingStandard\*'
    ],
];

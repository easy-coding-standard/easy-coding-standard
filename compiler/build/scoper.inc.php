<?php

declare(strict_types=1);

use Nette\Utils\Strings;

require_once __DIR__ . '/../vendor/autoload.php';

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

        // scoping stupid string contact class naming from: https://github.com/squizlabs/PHP_CodeSniffer/blob/b477a41ac565dad7a01c8b42f4804280723ad02f/src/Files/File.php#L562

        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::endsWith($filePath, 'src/Files/File.php')) {
                return $content;
            }

            // $tokenizerClass = 'PHP_CodeSniffer\\Tokenizers\\' . $this->tokenizerType; →
            // $tokenizerClass = 'MagicScoperPrefix\\PHP_CodeSniffer\\Tokenizers\\' . $this->tokenizerType;
            return Strings::replace($content, '#\'PHP_CodeSniffer#', sprintf("'%s\\\\PHP_CodeSniffer", $prefix));
        },
    ],
    'whitelist' => [
        // needed for autoload, that is not prefixed, since it's in bin/* file
        'Symplify\*',
    ],
];

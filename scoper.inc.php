<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Nette\Utils\DateTime;
use ECSPrefix20210507\Nette\Utils\Strings;
use ECSPrefix20210507\Isolated\Symfony\Component\Finder\Finder;
$finder = new \ECSPrefix20210507\Isolated\Symfony\Component\Finder\Finder();
$polyfillFileInfos = $finder->files()->in(__DIR__ . '/vendor/symfony/polyfill-*')->name('*.php')->getIterator();
$polyfillFilePaths = [];
foreach ($polyfillFileInfos as $polyfillFileInfo) {
    $polyfillFilePaths[] = $polyfillFileInfo->getPathname();
}
$dateTime = DateTime::from('now');
$timestamp = $dateTime->format('Ymd');
// see https://github.com/humbug/php-scoper
return ['prefix' => 'ECSPrefix' . $timestamp, 'files-whitelist' => [
    // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
    // these paths are relative to this file location, so it should be in the root directory
    'vendor/symfony/deprecation-contracts/function.php',
] + $polyfillFilePaths, 'whitelist' => [
    // needed for autoload, that is not prefixed, since it's in bin/* file
    'Symplify\\*',
    'PhpCsFixer\\*',
    'PHP_CodeSniffer\\*',
    'ECSPrefix20210507\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
    'ECSPrefix20210507\\Symfony\\Component\\DependencyInjection\\Extension\\ExtensionInterface',
    'ECSPrefix20210507\\Composer\\InstalledVersions',
], 'patchers' => [
    function (string $filePath, string $prefix, string $content) : string {
        if (!Strings::endsWith($filePath, 'vendor/jean85/pretty-package-versions/src/PrettyVersions.php')) {
            return $content;
        }
        // see https://regex101.com/r/v8zRMm/1
        return Strings::replace($content, '#' . $prefix . '\\\\Composer\\\\InstalledVersions#', 'ECSPrefix20210507\\Composer\\InstalledVersions');
    },
    // fixes https://github.com/symplify/symplify/issues/3102
    function (string $filePath, string $prefix, string $content) : string {
        if (!Strings::contains($filePath, 'vendor/')) {
            return $content;
        }
        // @see https://regex101.com/r/lBV8IO/2
        $fqcnReservedPattern = \sprintf('#(\\\\)?%s\\\\(parent|self|static)#m', $prefix);
        $matches = Strings::matchAll($content, $fqcnReservedPattern);
        if (!$matches) {
            return $content;
        }
        foreach ($matches as $match) {
            $content = \str_replace($match[0], $match[2], $content);
        }
        return $content;
    },
]];

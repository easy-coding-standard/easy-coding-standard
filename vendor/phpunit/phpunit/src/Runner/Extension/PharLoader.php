<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\Runner\Extension;

use ECSPrefix20210803\PharIo\Manifest\ApplicationName;
use ECSPrefix20210803\PharIo\Manifest\Exception as ManifestException;
use ECSPrefix20210803\PharIo\Manifest\ManifestLoader;
use ECSPrefix20210803\PharIo\Version\Version as PharIoVersion;
use ECSPrefix20210803\PHPUnit\Runner\Version;
use ECSPrefix20210803\SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class PharLoader
{
    /**
     * @psalm-return array{loadedExtensions: list<string>, notLoadedExtensions: list<string>}
     */
    public function loadPharExtensionsInDirectory(string $directory) : array
    {
        $loadedExtensions = [];
        $notLoadedExtensions = [];
        foreach ((new \ECSPrefix20210803\SebastianBergmann\FileIterator\Facade())->getFilesAsArray($directory, '.phar') as $file) {
            if (!\is_file('phar://' . $file . '/manifest.xml')) {
                $notLoadedExtensions[] = $file . ' is not an extension for PHPUnit';
                continue;
            }
            try {
                $applicationName = new \ECSPrefix20210803\PharIo\Manifest\ApplicationName('phpunit/phpunit');
                $version = new \ECSPrefix20210803\PharIo\Version\Version(\ECSPrefix20210803\PHPUnit\Runner\Version::series());
                $manifest = \ECSPrefix20210803\PharIo\Manifest\ManifestLoader::fromFile('phar://' . $file . '/manifest.xml');
                if (!$manifest->isExtensionFor($applicationName)) {
                    $notLoadedExtensions[] = $file . ' is not an extension for PHPUnit';
                    continue;
                }
                if (!$manifest->isExtensionFor($applicationName, $version)) {
                    $notLoadedExtensions[] = $file . ' is not compatible with this version of PHPUnit';
                    continue;
                }
            } catch (\ECSPrefix20210803\PharIo\Manifest\Exception $e) {
                $notLoadedExtensions[] = $file . ': ' . $e->getMessage();
                continue;
            }
            /**
             * @noinspection PhpIncludeInspection
             * @psalm-suppress UnresolvableInclude
             */
            require $file;
            $loadedExtensions[] = $manifest->getName()->asString() . ' ' . $manifest->getVersion()->getVersionString();
        }
        return ['loadedExtensions' => $loadedExtensions, 'notLoadedExtensions' => $notLoadedExtensions];
    }
}
